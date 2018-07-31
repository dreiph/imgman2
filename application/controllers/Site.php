<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Controller {
	public function __construct()
	{
			parent::__construct();
			session_start();
			// $this->output->enable_profiler(true);
	}
	public function login()
	{
		$data=array();
		if(isset($_POST['username']) && isset($_POST['password']))
		{
			$username=$this->input->post('username');
			$password=$this->input->post('password');
			
			$q=$this->db->query("SELECT uid, active, role FROM users WHERE username=? AND password=sha2(?,224) LIMIT 1", array($username,$password));
			
			if($q->num_rows() == 1)
			{
				if($q->row()->active==1)
				{
					$_SESSION['loggedin']=true;
					$_SESSION['username']=$username;
					$_SESSION['role']=$q->row()->role;
					redirect('site/index', 'refresh');
				}
				else
				{
					$data['error']="Your account is disabled.";
				}
			}
			else
			{
				$data['error']="Incorrect username and/or password";
			}
		}
		$this->load->view('login', $data);
	}
	public function logout()
	{
		unset($_SESSION);
		session_destroy();
		redirect('site/login', 'refresh');
	}
	public function index()
	{
		if(!isset($_SESSION['loggedin']))
		{
			redirect('site/login');
		}
		$data=array();
		
		//stats
		$q=$this->db->query("SELECT uid FROM images");
		$data['stats_count']=$q->num_rows();
		
		//image dimensions for filtering
		$q=$this->db->query("SELECT DISTINCT(img_dimensions) as imde FROM images ORDER BY imde");
		$data['image_dimensions']=$q->result();
		
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
		
		$config['first_link'] = 'First';
		$config['last_link'] = 'Last';
		
		$config['base_url'] = base_url().'site/index/';
		$config['per_page'] = 10;
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
		
		if(isset($_SESSION['imde']))
		{
			$sql_imde=" AND img_dimensions='".$_SESSION['imde']."'";
		}
		else
		{
			$sql_imde="";
		}
		
		if(isset($_SESSION['sq']))
		{
			$q=$this->db->query("SELECT * FROM images WHERE img_dimensions LIKE '%".$_SESSION['sq']."%' OR img_upload_filename LIKE '%".$_SESSION['sq']."%' OR img_system_filename LIKE '%".$_SESSION['sq']."%' ".$sql_imde." ORDER BY ".$_SESSION['order']." ".$_SESSION['sort']." LIMIT ?, ?", array((int)$page,(int)$config['per_page']));
			
			$config['total_rows'] = $this->db->query("SELECT uid FROM images WHERE img_dimensions LIKE '%".$_SESSION['sq']."%' OR img_upload_filename LIKE '%".$_SESSION['sq']."%' OR img_system_filename LIKE '%".$_SESSION['sq']."%'".$sql_imde)->num_rows();
		}
		else
		{
			$q=$this->db->query("SELECT * FROM images WHERE 1=1 ".$sql_imde." ORDER BY ".$_SESSION['order']." ".$_SESSION['sort']." LIMIT ?, ?", array((int)$page,(int)$config['per_page']));
			
			$config['total_rows'] = $this->db->query("SELECT uid FROM images WHERE 1=1".$sql_imde)->num_rows();
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
		if(!isset($_SESSION['loggedin']))
		{
			redirect('site/login');
		}
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
		if($action=='imde')
		{
			if(isset($_GET['image_dimensions']))
			{
				$image_dimensions=$this->input->get('image_dimensions', true);
				if($image_dimensions=="ALL")
				{
					unset($_SESSION['imde']);
				}
				else
				{
					$_SESSION['imde']=$image_dimensions;
				}
			}
		}
		redirect('site/index','refresh');
	}
	public function search()
	{
		if(!isset($_SESSION['loggedin']))
		{
			redirect('site/login');
		}
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
		if(!isset($_SESSION['loggedin']))
		{
			redirect('site/login');
		}
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
	public function delete()
	{
		$data=array();
		if($_SESSION['role']=='admin')
		{
			if(isset($_POST['uid']))
			{
				$uid=$this->input->post('uid', true);
				
				//delete image
				$q=$this->db->query("SELECT img_system_filename FROM images WHERE uid=? LIMIT 1", array($uid));
				if($q->num_rows()==1)
				{
					$image_delete=$q->row()->img_system_filename;
					if(unlink(FCPATH.$this->config->item('cdn_url').$image_delete))
					{
						//delete entry from DB
						$q=$this->db->query("DELETE FROM images WHERE uid=? LIMIT 1", array($uid));
						$data['msg']="Successfully deleted image #".$uid;
						redirect('site/index/?msg='.$data['msg'], 'refresh');
					}
					else
					{
						die("Unable to deleteimage");
					}
				}
				else
				{
					die("Unknown img_system_filename!");
				}
			}
			else
			{
				die("Unknown ID to delete!");
			}
		}
		else
		{
			die("You don't have permission to delete files!");
		}
	}
	public function reset()
	{
		session_destroy();
	}
}
