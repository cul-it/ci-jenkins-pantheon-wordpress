<?php

  namespace AcfBetterSearch\Settings;

  class _Core
  {
    public function __construct()
    {
      new Acf();
      new Config();
      new Options();
      new Page();
    }
  }