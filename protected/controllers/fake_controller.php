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
        var_dump($_GET['last_block']);
        $fake = new fake_class();
        $fake->proceed($_GET['last_block']);
    }
}