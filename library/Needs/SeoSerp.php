<?php
class SeoSerp{
	public $_title;
	public $_description;
	public $_keywords;

    public function setTitle($title)
    {
        $this->_title = (string) $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setDescription($description)
    {
        $this->_description = (string) $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function setKeywords($keywords)
    {
        $this->_keywords = (string) $keywords;
        return $this;
    }

    public function getKeywords()
    {
        return $this->_keywords;
    }

	public function setSeo($controller = null, $action = null, $id = null, $page = NULL)
	{
		switch ($controller){
			case "index":
				$this->indexIndex($page);
				break;
			case "catalog":
				// PRODUCTS
				switch ($action) {
					// INDEX
//					case "index":
//						$this->productsIndex();
//					break;
					// ADDTOFAVORITES
					case "addtofavorites":
						$this->addToFavorites($id);
						break;
					// DETAILS
					case "product-details":
						$this->catalogDetails($id);
					break;
					// DETAILS
					case "categories":
						$this->productCategories($id, $page);
					break;
					// TAGS
					case "tags":
						$this->productTags($id, $page);
					break;
					// TAGS
					case "cloudtag":
						$this->productCloudtag($id);
					break;
					// TAGS
					case "tagcloud":
						$this->productTagcloud($id);
					break;
					// DEFAULT
					default:
						$this->defaultSeo();
					break;
				}
			break;
			// ACCOUNT
			case "account":
				switch($action){
					// NEW
					case "new":
						$this->accountNew();
					break;
					// TERMS
					case "terms":
						$this->accountTerms();
					break;
					// DEFAULT
					default:
						$this->defaultSeo();
					break;
				}
			break;
			// AUTH
			case "auth":
				switch($action){
				// FORGOT PASSWORD
				case "forgot-password":
					$this->authForgotPassword();
				break;
				// DEFAULT
				default:
					$this->defaultSeo();
				break;
				}
			break;
			// CMS
			case "cms":
				switch($action){
				// VIEW
				case "view":
					$this->cmsView($id);
				break;
				// DEFAULT
				default:
					$this->defaultSeo();
				break;
				}
			break;
			// AJAX
			case "ajax":
				switch($action){
					case "fbshare":
						$this->ajaxFbshare();
					break;
					case "subscribe":
						$this->ajaxSubscribe();
					break;
				}
			// CONTACTUS
			case "contact":
				switch($action){
					case "index":
						$this->contactIndex();
					break;
					default:
						$this->defaultSeo();
					break;
				}
			break;
			// DEFAULT
			default:
				$this->defaultSeo();
			break;
		}
	}

	public function indexIndex($page = NULL)
	{
		if(NULL != $page)
		{
			$pagina = (NULL != $page && $page != 1)?" - pagina ".$page." ":"";
			$this->setTitle('Pitipoance sexy'.$pagina.' | SexyPitipoanca.ro');
			$this->setDescription('Sexy Pitipoanca site romanesc dedicat galeriilor de imagini cu pitipoance sexy si cocalari.');
			$this->setKeywords('sexy, pitipoanca, pitipoance, cocalari, cocalar');
		}
		else
		{
			$this->defaultSeo();
		}
	}

	public function ajaxFbshare()
	{
		$this->setTitle('Share pe Facebook | Pitipoance sexy - SexyPitipoanca.ro');
		$this->setDescription("Share pe Facebook. Pitipoance sexy si cocalari.");
		$this->setKeywords("sexy, pitipoanca, pitipoance, cocalari, cocalar");
		return true;
	}

	public function ajaxSubscribe()
	{
		$this->setTitle('Abonare la newsletter | Pitipoance sexy - SexyPitipoanca.ro');
		$this->setDescription("Abonare la newsletter. Pitipoance sexy si cocalari.");
		$this->setKeywords("sexy, pitipoanca, pitipoance, cocalari, cocalar");
		return true;
	}
	
	public function defaultSeo()
	{
		$this->setTitle(SLOGAN);
		$this->setDescription(WEBSITE_NAME." site romanesc dedicat galeriilor de imagini cu pitipoance sexy si cocalari.");
		$this->setKeywords("sexy, pitipoanca, pitipoance, cocalari, cocalar");
		return true;
	}

	public function productsIndex()
	{
		$this->setTitle("Produse");
		$this->setDescription("Produse");
		$this->setKeywords("Produse");
	}

	public function catalogDetails($id = null)
	{
		if(null != $id){
			$catalog = new Default_Model_CatalogProducts();
			$catalog->find($id);
			if(null != $catalog){
				$this->setTitle($catalog->getName().' | '.WEBSITE_NAME);
				$this->setDescription($catalog->getName(). 'Sexy Pitipoanca site romanesc dedicat galeriilor de imagini cu pitipoance sexy si cocalari');
				$this->setKeywords($this->separateKeywords($catalog->getName()).", sexy, pitipoanca, pitipoance, cocalari, cocalar");
			}else{
				$this->defaultSeo();
				return false;
			}
		}else{
			$this->defaultSeo();
			return false;
		}
	}

	public function addToFavorites($id = null)
	{
		if(null != $id){
			$catalog = new Default_Model_CatalogProducts();
			$catalog->find($id);
			if(null != $catalog){
				$this->setTitle($catalog->getName().' - adauga la favorite | SexyPitipoanca.ro');
				$this->setDescription('Adauga la favorite: '.$catalog->getName());
				$this->setKeywords($this->separateKeywords($catalog->getName()).", sexy, pitipoanca, pitipoance, cocalari, cocalar");
			}else{
				$this->defaultSeo();
				return false;
			}
		}else{
			$this->defaultSeo();
			return false;
		}
	}

	public function productCategories($id = NULL, $page = NULL)
	{
		$pagina = (NULL != $page && $page != 1)?" - pagina ".$page." ":"";
		if(null != $id){
			$model = new Default_Model_CatalogCategories();
			if($model->find($id)){
				$this->setTitle($model->getName().$pagina." | ".WEBSITE_NAME);
				$this->setDescription($model->getName().$pagina.' | SexyPitipoanca.ro');
				$this->setKeywords($this->separateKeywords($model->getName()).", sexy, pitipoanca, pitipoance, cocalari, cocalar");
			} else {
				$this->defaultSeo();
				return false;
			}
		} else {
			$this->defaultSeo();
			return false;
		}
	}

	public function accountNew()
	{
		$this->setTitle('Creeaza-ti cont | '.WEBSITE_NAME);
		$this->setDescription('Creeaza-ti cont pe Sexy Pitipoanca pentru a putea accesa toate galeriile.');
		$this->setKeywords('creeare cont, pitipoance, sexy, pitipoanca');
	}

	public function accountTerms()
	{
		$this->setTitle('Termeni si Conditii de utilizare | '.WEBSITE_NAME);
		$this->setDescription('Pentru a creea un cont nou pe site-ul nostru trebuie sa fii de acord cu Termenii si Conditiile');
		$this->setKeywords('termeni, conditii, utilizare, pitipoance, sexy, pitipoanca');
	}

	public function authForgotPassword()
	{
		$this->setTitle('Recuperare parola | SexyPitipoanca.ro');
		$this->setDescription('Daca ti-ai pierdut parola foloseste formularul pentru a-ti reseta parola');
		$this->setKeywords('recuperare, parola, pitipoance, sexy, pitipoanca');
	}

	public function productTags($id = null, $page = null)
	{
		$pagina = (NULL != $page && $page != 1)?" - pagina ".$page." ":"";
		if(null != $id)
		{
			$this->setTitle('Tag: '.ucfirst($id).$pagina.' | SexyPitipoanca.ro');
			$this->setDescription('Tag: '.$id.$pagina.'Sexy Pitipoanca site romanesc dedicat galeriilor de imagini cu pitipoance sexy si cocalari.');
			$this->setKeywords(ucfirst($id).' ,pitipoance, sexy, pitipoanca');
		}
		else
		{
			$this->defaultSeo();
			return false;
		}
	}

	public function productCloudtag($id)
	{
		$this->setTitle('Tag cloud - tag: '.ucfirst($id).' | SexyPitipoanca.ro');
		$this->setDescription('TagCloud - tag: '.$id.' | Sexy Pitipoanca site romanesc dedicat galeriilor de imagini cu pitipoance sexy si cocalari.');
		$this->setKeywords(ucfirst($id).' ,pitipoance, sexy, pitipoanca');
	}

	public function productTagcloud()
	{
		$this->setTitle('Tag cloud | SexyPitipoanca.ro');
		$this->setDescription('Tagcloud | Sexy Pitipoanca site romanesc dedicat galeriilor de imagini cu pitipoance sexy si cocalari.');
		$this->setKeywords('pitipoance, sexy, pitipoanca');
	}

	public function contactIndex()
	{
		$this->setTitle("Pagina de contact - SexyPitipoanca.ro");
		$this->setDescription("Pagina de contact. Sexy Pitipoanca site romanesc dedicat galeriilor de imagini cu pitipoance sexy si cocalari.'");
		$this->setKeywords("contact, pitipoance, sexy");
	}

//////////////////////////////////////
//UTILITIES
//////////////////////////////////////
	public function separateKeywords($string){
		$result = "";
		if(null != $string){
			$result = preg_replace('/\s+/',' ', strtolower($string));
			$result = str_replace(' ', ', ', $result);
		}
		return $result;
	}
}
?>