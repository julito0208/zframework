<?php

class HTMLTableColumnImage extends HTMLTableColumn
{
	const DEFAULT_CLASS = 'image-column image';

	protected $_thumb_type;

	public function __construct($thumb_type=null, $title='&nbsp;')
	{
		parent::__construct(StringHelper::uniqid('image_'), $title, 1);
		$this->add_style('text-align', 'center');
		$this->_render_function = array($this, '_render_function_callback');
		$this->_thumb_type = $thumb_type ? $thumb_type : ZfImageThumbType::DEFAULT_THUMB_TYPE;
	}

	protected function _render_function_callback(GetParam $row, HTMLTableColumn $column)
	{
		if(ClassHelper::is_instance_of($row, 'Imageable'))
		{
			$image_url = ZfImageFile::get_image_url($row, $this->_thumb_type);

			$img = new HTMLShortTag('img');
			$img->set_param('alt', 'Image');
			$img->set_param('src', $image_url);

			return $img->to_string();
		}

		return '';
	}


	public function get_class()
	{
		$this->add_class(self::DEFAULT_CLASS);
		return parent::get_class();
	}

	public function prepare_params()
	{
		$this->add_class(self::DEFAULT_CLASS);
		parent::prepare_params();

	}
}