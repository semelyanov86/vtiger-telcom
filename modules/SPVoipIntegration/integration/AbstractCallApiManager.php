<?php
namespace Telcom\integration;

abstract class AbstractCallApiManager {
    public abstract function doOutgoingCall($number);
}