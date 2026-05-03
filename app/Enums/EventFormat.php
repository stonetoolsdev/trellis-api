<?php

namespace App\Enums;

enum EventFormat: string
{
  case InPerson = 'in_person';
  case Virtual = 'virtual';
  case Hybrid = 'hybrid';
}