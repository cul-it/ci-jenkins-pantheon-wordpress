<?php

  namespace AcfBetterSearch;

  class AcfBetterSearch
  {
    public function __construct()
    {
      new Admin\_Core();
      new Search\_Core();
      new Settings\_Core();
    }
  }