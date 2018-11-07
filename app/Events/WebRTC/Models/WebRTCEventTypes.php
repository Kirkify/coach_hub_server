<?php

namespace App\Events\WebRTC\Models;

abstract class WebRTCEventTypes
{
    const ConnectionRequest = 1;
    const AcceptConnectionRequest = 2;
    const IceCandidates = 3;
}