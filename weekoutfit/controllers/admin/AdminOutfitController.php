<?php
class AdminOutfitController extends ModuleAdminController
{
  public function init(){
    parent::init();
    Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . Tools::safeOutput($this->module->name));
  }
  public function initContent(){
    parent::initContent();

  }

}
