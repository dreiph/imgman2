<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Controller {
        public function __construct()
        {
                parent::__construct();
                session_start();
        }
	public function index()
	{
		// $this->output->enable_profiler(true);
		$data=array();
		
		//pagination
		$this->load->library('pagination');
		$config['full_tag_open'] = "<ul class='uk-pagination'>";
		$config['full_tag_close'] ="</ul>";
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = "<li class='uk-active'><span>";
		$config['cur_tag_close'] = "</span></li>";
		$config['next_tag_open'] = "<li>";
		$config['next_tagl_close'] = "</li>";
		$config['prev_tag_open'] = "<li>";
		$config['prev_tagl_close'] = "</li>";
		$config['first_tag_open'] = "<li>";
		$config['first_tagl_close'] = "</li>";
		$config['last_tag_open'] = "<li>";
		$config['last_tagl_close'] = "</li>";
		
		$config['first_link'] = 'Pirmas';
		$config['last_link'] = 'Paskutinis';
		
		$config['base_url'] = base_url().'site/index/';
		$config['per_page'] = 2;
		$config["uri_segment"] = 3;
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

		if(!isset($_SESSION['order']))
		{
			$_SESSION['order']='date';
		}				
		if(!isset($_SESSION['sort']))
		{
			$_SESSION['sort']='desc';
		}
		
		if(isset($_SESSION['sq']))
		{
			$q=$this->db->query("SELECT * FROM images WHERE img_dimensions LIKE '%".$_SESSION['sq']."%' OR img_upload_filename LIKE '%".$_SESSION['sq']."%' OR img_system_filename LIKE '%".$_SESSION['sq']."%' ORDER BY ".$_SESSION['order']." ".$_SESSION['sort']." LIMIT ?, ?", array((int)$page,(int)$config['per_page']));
			
			$config['total_rows'] = $this->db->query("SELECT uid FROM images WHERE img_dimensions LIKE '%".$_SESSION['sq']."%' OR img_upload_filename LIKE '%".$_SESSION['sq']."%' OR img_system_filename LIKE '%".$_SESSION['sq']."%'")->num_rows();
		}
		else
		{
			$q=$this->db->query("SELECT * FROM images WHERE 1=1 ORDER BY ".$_SESSION['order']." ".$_SESSION['sort']." LIMIT ?, ?", array((int)$page,(int)$config['per_page']));
			
			$config['total_rows'] = $this->db->query("SELECT uid FROM images WHERE 1=1")->num_rows();
		}

		$this->pagination->initialize($config);

		$data['links']=$this->pagination->create_links();
		
		if($q->num_rows()>0)
		{
			$data['results']=$q->result();
		}
		else
		{
			$data['error']="No images yet";
		}
		
		$this->load->view('intro', $data);
	}
	
	public function action($action)
	{
		if($action=='date_asc')
		{
			$_SESSION['order']='datetime_uploaded';
			$_SESSION['sort']='asc';
		}
		if($action=='date_desc')
		{
			$_SESSION['order']='datetime_uploaded';
			$_SESSION['sort']='desc';
		}
		redirect('site/index','refresh');
	}
	public function search()
	{
		if(isset($_POST['sq']))
		{
			if($_POST['sq']<>""){
				$_SESSION['sq']=$this->input->post('sq');
			}
			else
			{
				if(isset($_SESSION['sq']))
				{
					unset($_SESSION['sq']);
				}
			}
		}
		redirect('site/index','refresh');
	}
	
	public function upload()
	{
		var_dump($_FILES);
		$config['upload_path']          = './uploads/';
		$config['allowed_types']        = 'gif|jpg|png';
		$config['max_size']             = 10000;
		$config['max_width']            = 1024;
		$config['max_height']           = 768;

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('userfile'))
		{
				$error = array('error' => $this->upload->display_errors());

				$this->load->view('upload_form', $error);
		}
		else
		{
				$data = array('upload_data' => $this->upload->data());

				$this->load->view('upload_success', $data);
		}
	}
}
