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
		$config['per_page'] = 3;
		$config["uri_segment"] = 3;
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

		if(!isset($_SESSION['order']))
		{
			$_SESSION['order']='datetime_uploaded';
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
		if(!isset($_FILES))
		{
			die("You did not try to upload file");
		}
		$config['upload_path']          = './public/cdn/';
		$config['allowed_types']        = 'gif|jpg|jpeg|png';
		$config['file_ext_tolower']     = true;
		$config['overwrite'] 		    = false;
		$config['max_filename'] 		= 255;
		$config['remove_spaces'] 		= true;
		$config['encrypt_name'] 		= true;
		$config['detect_mime'] 			= true;
		$config['mod_mime_fix'] 		= true;
		$config['max_size']             = 20480;
		$config['max_width']            = 20000;
		$config['max_height']           = 20000;

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('userfile'))
		{
				$error = array('error' => $this->upload->display_errors());

				var_dump($error);
		}
		else
		{
				$data = array('upload_data' => $this->upload->data());
				var_dump($data);

				$q=$this->db->query("INSERT INTO images (
				img_dimensions, 
				img_filesize, 
				img_system_filename, 
				img_upload_filename, 
				datetime_uploaded, 
				user_uploaded, 
				ip
				) VALUES(?,?,?,?,?,?,?)", array(
				$data['upload_data']['image_width'].'x'.$data['upload_data']['image_height'], 
				$data['upload_data']['file_size'], 
				"public/cdn/".$data['upload_data']['file_name'], 
				$data['upload_data']['orig_name'], 
				date("Y-m-d H:i:s"), 
				'admin', 
				$_SERVER['REMOTE_ADDR'])
				);
		}
	}
	
	public function reset()
	{
		session_destroy();
	}
}
