<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

/**
 * fungsi untuk mengubah format tanggal
 *
 * @return void
 */
function hari_ini()
{
    $today = date('l');
    $days = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jum\'at',
        'Saturday' => 'Sabtu'
    ];

    return $days[$today];
}

/**
 * fungsi untuk jam asia/jakarta
 *
 * @return void
 */
function jam_sekarang()
{
    return Carbon::now('Asia/Jakarta')->format('H:i');
}


if (!function_exists('setActive')) { // jika fungsi tidak ada
    /**
     * fungsi untuk menentukan menu aktif
     *
     * @param  mixed $uri
     * @param  mixed $output
     * @return void
     */
    function setActive($uri, $output = 'active')
    {
        if (is_array($uri)) { // jika uri berupa array
            foreach ($uri as $u) { // looping uri
                if (Route::is($u)) { // jika route sama dengan uri
                    return $output;  // maka output
                }
            }
        } else { // jika uri bukan array
            if (Route::is($uri)) { // jika route sama dengan uri
                return $output;
            }
        }
    }
}

if (!function_exists('d_block')) {
    /**
     * fungsi untuk menentukan menu aktif untuk display block
     *
     * @param  mixed $uri
     * @param  mixed $output
     * @return void
     */
    function d_block($uri, $output = 'display: block;')
    {
        if (is_array($uri)) {
            foreach ($uri as $u) {
                if (Route::is($u)) {
                    return $output;
                }
            }
        } else {
            if (Route::is($uri)) {
                return $output;
            }
        }
    }
}

if (!function_exists('menuOpen')) {
    /**
     * fungsi untuk menentukan menu open aktif
     *
     * @param  mixed $uri
     * @param  mixed $output
     * @return void
     */
    function menuOpen($uri, $output = 'menu-is-opening menu-open')
    {
        if (is_array($uri)) {
            foreach ($uri as $u) {
                if (Route::is($u)) {
                    return $output;
                }
            }
        } else {
            if (Route::is($uri)) {
                return $output;
            }
        }
    }
}

/**
 * function untuk menghilangkan tag html
 *
 * @param  mixed $html
 * @return void
 */
function bersihkanHTML($html) {
    return htmlspecialchars($html);
}

/**
 * function untuk menghilangkan tag html
 * serta mengubah tag html menjadi string
 *
 * @param  mixed $html
 * @return void
 */
function htmlStrips($html) {
    return strip_tags(htmlspecialchars($html));
}
