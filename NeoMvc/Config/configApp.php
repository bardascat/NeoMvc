<?php

namespace NeoMvc\Config;

class configApp {

    function __construct() {
        session_start();
        define("URL", "http://localhost/neomvc/");
        define("WEBSITE_NAME", "NeoMvc");
        define("LIVE", "TRUE");
    }

}

?>