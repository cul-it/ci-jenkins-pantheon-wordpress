<?php

  namespace AcfBetterSearch\Admin;

  class _Core
  {
    public function __construct()
    {
      new Acf();
      new Assets();
      new Install();
      new Notice();
      new Plugin();
      new Uninstall();
    }
  }