<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('format_indo')) {
    function format_indo($date)
    {
        $BulanIndo = array("Jan", "Feb", "Mar", "Apr", "Mei", "Juni", "Juli", "Agust", "Sept", "Okt", "Nov", "Des");

        $tahun = substr($date, 0, 4);
        $bulan = substr($date, 5, 2);
        $tgl   = substr($date, 8, 2);
        $result = $tgl . " " . $BulanIndo[(int) $bulan - 1] . " " . $tahun;
        return ($result);
    }


}

if ( ! function_exists('bulan'))

{

    function bulan($bln)

    {

        switch ($bln)

        {

            case 1:

                return "Januari";

                break;

            case 2:

                return "Februari";

                break;

            case 3:

                return "Maret";

                break;

            case 4:

                return "April";

                break;

            case 5:

                return "Mei";

                break;

            case 6:

                return "Juni";

                break;

            case 7:

                return "Juli";

                break;

            case 8:

                return "Agustus";

                break;

            case 9:

                return "September";

                break;

            case 10:

                return "Oktober";

                break;

            case 11:

                return "November";

                break;

            case 12:

                return "Desember";

                break;

        }

    }

}

