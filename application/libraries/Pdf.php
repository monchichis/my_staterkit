<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf extends Dompdf{
    public $filename;
    
    public function __construct(){
        parent::__construct();
        $this->filename = "laporan.pdf";
    }
    
    protected function ci()
    {
        return get_instance();
    }
    
    public function load_view($view, $data = array()){

        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->setChroot(FCPATH);
        
        $this->setOptions($options);

        $html = $this->ci()->load->view($view, $data, TRUE);
        $this->load_html($html);
        // Render the PDF
        $this->render();
            // Output the generated PDF to Browser
               $this->stream($this->filename, array("Attachment" => false));
    }
}