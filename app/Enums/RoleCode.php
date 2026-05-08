<?php

namespace App\Enums;

enum RoleCode: string
{
    case ADMIN = 'admin';
    case EMPLOYEE = 'pegawai';
    case HR = 'sdm';
}
