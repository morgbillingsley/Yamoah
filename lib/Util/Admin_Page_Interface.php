<?php

namespace Yamoah\Util;

/**
 * Interface: Admin_Page_Interface
 */
interface Admin_Page_Interface
{
    public function handle_post();
    public function display();
    public function build();
}

?>