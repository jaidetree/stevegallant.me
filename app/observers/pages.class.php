<?php 
use jframe\APP as APP;
class PagesObserver 
{
}
APP::modules()->observer->attach(new PagesObserver(), 'PagesController');
?>