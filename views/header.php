<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="Romanian" />
        <meta http-equiv="Content-Language" content="ro" />
        <title><? echo $this->pageName . ' - ' . WEBSITE_NAME ?></title>
        <meta name="description" content="<? if ($this->getMeta()) echo $this->getMeta() ?>"/>
        <link rel="shortcut icon"  type="image/png"  href="<? echo URL ?>layout/favicon.ico">
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
    <body>

        <div id="wrapper"> <!-- wrapper -->

           