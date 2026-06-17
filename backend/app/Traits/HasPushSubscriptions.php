<?php

namespace App\Traits;

trait HasPushSubscriptions
{
    public function pushSubscriptions()
    {
        return $this->hasMany(\App\Models\PushSubscription::class);
    }

    public function routeNotificationForWebPush()
    {
        return $this->pushSubscriptions;
    }

    public function updatePushSubscription($endpoint, $key = null, $token = null, $contentEncoding = null)
    {
        $subscription = $this->pushSubscriptions()->where('endpoint', $endpoint)->first();

        if ($subscription) {
            $subscription->public_key = $key;
            $subscription->auth_token = $token;
            $subscription->content_encoding = $contentEncoding;
            $subscription->save();

            return $subscription;
        }

        return $this->pushSubscriptions()->create([
            'endpoint' => $endpoint,
            'public_key' => $key,
            'auth_token' => $token,
            'content_encoding' => $contentEncoding,
        ]);
    }

    public function deletePushSubscription($endpoint)
    {
        return $this->pushSubscriptions()->where('endpoint', $endpoint)->delete();
    }
}
