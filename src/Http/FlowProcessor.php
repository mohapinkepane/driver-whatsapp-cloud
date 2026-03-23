<?php

namespace Botman\Drivers\Whatsapp\Http;

use Illuminate\Http\Request;
use Botman\Drivers\Whatsapp\Traits\ValidatesFlowToken;
use Botman\Drivers\Whatsapp\Traits\MatchesFlowProfile;
use Botman\Drivers\Whatsapp\Traits\HandlesFlowEncryption;
use Botman\Drivers\Whatsapp\Traits\ValidatesFlowSignature;

abstract class FlowProcessor
{
    use HandlesFlowEncryption, ValidatesFlowSignature, MatchesFlowProfile, ValidatesFlowToken;

    /**
     * Main entry point — validates, decrypts, dispatches, encrypts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleFlow(Request $request)
    {
        if (! $this->matchesFlowProfile($request)) {
            return response([], 422);
        }

        $app_secret    = config('botman.whatsapp.app_secret');
        $validSignature = empty($app_secret) || $this->validatesSignature($request);

        if (! $validSignature) {
            return response([], 432);
        }

        // TODO: Uncomment this block and add your flow token validation logic.
        // If the flow token becomes invalid, return HTTP code 427 to disable the flow
        // and show the message in `error_msg` to the user.
        // Refer to: https://developers.facebook.com/docs/whatsapp/flows/reference/error-codes#endpoint_error_codes
        //
        // if (!$this->validatesFlowToken($decryptedBody['flow_token'])) {
        //     $error_response = ['error_msg' => 'The message is no longer available'];
        //     return response($this->encryptResponse($error_response), 427);
        // }

        $decrypted_data = $this->decryptRequest($request);

        $response = $this->getResponse($decrypted_data['decryptedBody']);

        $encrypted_response = $this->encryptResponse($response);

        return response($encrypted_response);
    }

    /**
     * Dispatches the decrypted body to the appropriate handler.
     *
     * Handles cross-version concerns (ping, error, flow_token_signature)
     * before delegating to the concrete {@see getNextScreen()} implementation.
     *
     * Data API 4.0 note: the decrypted body may contain a `flow_token_signature`
     * field which concrete classes can optionally use for additional verification.
     *
     * @param  array  $decrypted_body
     * @return array
     */
    public function getResponse(array $decrypted_body): array
    {
        $action = $decrypted_body['action'] ?? null;
        $data   = $decrypted_body['data']   ?? [];

        if ($action === 'ping') {
            return $this->respondToPing();
        }

        if (! empty($data['error'])) {
            return $this->respondToError($data);
        }

        return $this->getNextScreen($decrypted_body);
    }

    /**
     * Implement this method to return the next screen payload.
     *
     * The $decrypted_body array will contain at minimum:
     *   - `screen`     (string)  — current screen ID
     *   - `action`     (string)  — e.g. "INIT", "data_exchange"
     *   - `flow_token` (string)  — unique token for this flow session
     *   - `data`       (array)   — user-submitted form data for the current screen
     *
     * Data API 4.0 additionally provides:
     *   - `flow_token_signature` (string|null) — optional; verify against your
     *     app secret for extra security. See:
     *     https://developers.facebook.com/docs/whatsapp/flows/guides/implementingyourflowendpoint#data_exchange_request
     *
     * Return an array with at minimum:
     *   - `screen` (string) — the screen to render next (or "SUCCESS" to complete)
     *   - `data`   (array)  — data payload for that screen
     *
     * @param  array  $decrypted_body
     * @return array
     */
    abstract public function getNextScreen(array $decrypted_body): array;

    /**
     * Respond to a health-check ping from the WhatsApp platform.
     *
     * @return array
     */
    public function respondToPing(): array
    {
        return ['data' => ['status' => 'active']];
    }

    /**
     * Acknowledge a client-side error reported by the platform.
     *
     * @param  array  $data
     * @return array
     */
    public function respondToError(array $data): array
    {
        return ['data' => ['acknowledged' => true]];
    }
}