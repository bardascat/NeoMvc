<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="Romanian" />
        <meta http-equiv="Content-Language" content="ro" />
        <title><?php echo $this->pageName . ' - ' . WEBSITE_NAME ?></title>
        <link href='https://fonts.googleapis.com/css?family=Oxygen&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
            <link rel="shortcut icon"  type="image/png"  href="">
                <?
                if (isset($this->globalJs)) {
                    foreach ($this->globalJs as $js)
                        echo "<script type='text/javascript' src='" . URL . $js . "'></script>\n";
                }
                if (isset($this->globalCss)) {
                    foreach ($this->globalCss as $css)
                        echo "<link rel='stylesheet' type='text/css' href='" . URL . $css . "' />\n";
                }
                ?>
                </head>

                <div id="wrapper">
                    <div id="header">
                        <div class="admin_icon">
                            <a href='<?=URL?>admin'>
                                <img src="<?= URL ?>images/admin/admin_icon.png" width="70"/>
                            </a>
                        </div>
                        <h2><?=$this->pageName?></h2>
                        <div class="menu">
                            <ul>
                                <li><a href="<?php echo URL ?>">oringo.ro</a></li>
                                <li><a href="<?php echo URL ?>admin/index/logout">Logout</a></li>
                            </ul>
                        </div>
                    </div>