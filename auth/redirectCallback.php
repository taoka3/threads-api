<?php
require '../lib/threads.php';
(new threads)->redirectCallback()->getAccessToken()->changeLongAccessToken()->save();
