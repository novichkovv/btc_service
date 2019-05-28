<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 28/05/2019
 * Time: 17:39
 */
class fake_controller extends controller
{
    public function proceed()
    {
        $fake = new fake_class();
        $fake->proceed($_GET['last_block']);
    }
}