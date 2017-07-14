<?php
/*
Plugin Name: LinkBuy Wordpress
Plugin URI: http://www.linkbuy.com.br/
Description: Instale o plugin LinkBuy Wordpress no seu Blog e ganhe muito dinheiro monetizando seus links.
Version: 3.7
Author: LinkBuy (suporte@linkbuy.com.br)
Author URI: http://www.linkbuy.com.br/
License: GPL
*/

class LinkBuy{
	
	const VERSAO_PLUGIN = "v3.7";
	const TEMPO_CACHE = 0;
	const TEMPO_TIMEOUT = 20;

	public function ativar(){

		if(!get_option('linkbuy_ParceiroURL')){

			$dominio_site = self::getDomain(get_option('home'));
			add_option('linkbuy_ParceiroURL',	$dominio_site);

		}else if(get_option('linkbuy_ParceiroURL')==NULL || get_option('linkbuy_ParceiroURL')==""){
			$dominio_site = self::getDomain(get_option('home'));
			if($dominio_site==false)
				update_option('linkbuy_ParceiroURL', self::getDomain(get_option('siteurl')));
			else
				update_option('linkbuy_ParceiroURL', $dominio_site);
		}
		
		if(!get_option('linkbuy_ParceiroID'))		add_option('linkbuy_ParceiroID',	NULL);
		if(!get_option('linkbuy_Excecoes'))			add_option('linkbuy_Excecoes',		NULL);
		if(!get_option('linkbuy_ck_op_video'))		add_option('linkbuy_ck_op_video',	false);
	}
	
	public function desativar(){
		delete_option('linkbuy_ParceiroURL');
	}
	public function criarMenu(){
		add_menu_page("LinkBuy ".self::VERSAO_PLUGIN."", "LinkBuy ".self::VERSAO_PLUGIN."",10, 'linkbuy-wordpress/linkbuy-config.php');
	}
	
	///// Métodos de Acesso
	public function getOpVideo(){
		return get_option('linkbuy_ck_op_video');
	}
	public function getID(){
		return get_option('linkbuy_ParceiroID');
	}
	public function getURL(){
		return get_option('linkbuy_ParceiroURL');
	}
	public function getExcecoes(){
		return get_option('linkbuy_Excecoes');
	}
	// Final Métodos de acesso
	
	public function _acentuacaoSanitize($texto){
		$trocarIsso = array('à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ü','ú','ÿ','À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','O','Ù','Ü','Ú','Ÿ',);
		$porIsso = array('a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','y','A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','0','U','U','U','Y',);
		$titletext = str_replace($trocarIsso, $porIsso, $texto);
		return $titletext;
	}
	
	public function generateSlug($title){
		$result = strtolower(self::_acentuacaoSanitize($title));
		$result = preg_replace("/[^a-z0-9\s-]/", "", $result);
		$result = trim(preg_replace("/[\s-]+/", " ", $result));
		$result = trim($result);
		$result = explode(' ',$result);
		$result = array_map('ucfirst',$result);
		$result = implode('-',$result);
		$result = preg_replace("/\s/", "-", $result);
		if(!empty($result))
//			return $result.'.zip';
			return $result;
		else
			return;
	}

	public function getDomain($url){
	  $pieces = parse_url($url);
	  $domain = isset($pieces['host']) ? $pieces['host'] : '';
	  if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)){
		return $regs['domain'];
	  }else{
		$url = str_replace(array("https://","ftp://"),"",$url);
		preg_match("/^(http:\/\/)?([^\/]+)/i",$url, $matches);
		$host = $matches[2];
		preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
		if($matches[0])
			return $matches[0];
		else
			return false;
	  }
	}
	
	public function isImagem($url){
		$fatias = explode(".",$url);
		$array_imagens = array("jpg","jpeg","png","gif","bmp");
		if(in_array(array_pop($fatias),$array_imagens))
			return true;
		else
			return false;
	}
	
	public function AtivarPlugin($conteudo){
		return self::ProtetorLinkBUY($conteudo);
	}

	public function LinkBuyHeaders(){
		$meta_og = self::MetaPropertyOG();

//		if(self::getOpVideo())
//			$video = self::LinkBuyVideo();
//		echo $meta_og . $video;
		echo $meta_og;

	}

	public function LinkBuyVideo(){
		global $post;
		$title_post = base64_encode($post->post_title);
		$id_afiliado = self::getID();
		return "
\n\n<!-- LinkBuy Codificar Video - by LinkBuy ".self::VERSAO_PLUGIN." -->
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js'></script>
<script type='text/javascript' src='http://www.linkbuy.com.br/scripts/linkbuy_conteudo.php?id=$id_afiliado&title=$title_post'></script>
<!-- END LinkBuy Codificar Video - by LinkBuy ".self::VERSAO_PLUGIN." -->\n\n";
	}

	public function MetaPropertyOG(){
		global $post;
		preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		if(count($matches[1][0])>0)
			$post_image = $matches[1][0];
		else
			$post_image = "https://www.linkbuy.com.br/img/cover.png";

		$post_title = $post->post_title;
		return "
\n\n<!-- LinkBuy Post Thumbnail - by LinkBuy ".self::VERSAO_PLUGIN." -->
<meta property=\"og:title\" content=\"$post_title\" />
<meta property=\"og:image\" content=\"$post_image\" />
<!-- End LinkBuy Post Thumbnail - by LinkBuy ".self::VERSAO_PLUGIN." -->\n\n";
	}
	
	public function criarCache($url,$codigo){
		$key = md5($url);
		set_transient($key,$codigo,self::TEMPO_CACHE);
		return true;
	}
	
//	public function removeDoCache($url){
//		$key = md5($url);
//		delete_transient($key);
//		return true;
//	}

	public function getCache($url){
		$key = md5($url);
		return get_transient($key);
	}
	
	public function linkBUYEncoder($id){

		global $post;
		$post_title = base64_encode($post->post_title);
		$post_permalink = get_permalink();
		
		$id = str_replace('#','',$id);
		
		$api_host = array();
		$api_host[0] = "http://184.107.146.34/~linkbuy/api/v4_json.php?id=$id&title=$post_title&permalink=$post_permalink";
		$api_host[1] = "http://184.107.146.35/~linkbuy/api/v4_json.php?id=$id&title=$post_title&permalink=$post_permalink";
		$api_host[2] = "http://184.107.146.36/~linkbuy/api/v4_json.php?id=$id&title=$post_title&permalink=$post_permalink";
		$api_host[3] = "http://184.107.146.37/~linkbuy/api/v4_json.php?id=$id&title=$post_title&permalink=$post_permalink";
		$total_hosts = sizeof($api_host) - 1;
		
		for($i=0;$i<=$total_hosts;$i++){

			$get_content = wp_remote_get($api_host[$i], array('timeout' => self::TEMPO_TIMEOUT));
			$get_body = wp_remote_retrieve_body($get_content);

			if(isset($get_body) && !empty($get_body))
				break;
		
		}
		
		$resposta_body = json_decode($get_body);

		if($resposta_body->erro==0 && !empty($resposta_body->id))
			return $resposta_body->id;
		else
			return "Invalido";
			
	}
		
	// public function ProtetorLinkBUY
	public function ProtetorLinkBUY($the_content){

		global $post;
		$PARCEIRO_ID = self::getID();
		$SITE_AFILIADO = self::getURL();
		$post_title_slug = self::generateSlug($post->post_title);
		
		preg_match_all('/href="([^"]*)"/i',$the_content,$resultados);
		$contagem = count($resultados[1]);

		$Execoes = self::getExcecoes();
		$array_dominios = explode(',',$Execoes);
		$array_dominios[] = $SITE_AFILIADO;
		$array_dominios[] = "linkbuy.com.br";
		
		for ($linha = 0; $linha < $contagem ; $linha++){

		$link = $resultados[1][$linha];
		$dominio = self::getDomain($link);
		
			if(!in_array($dominio,$array_dominios) && $dominio!=false){
				
				if(!self::isImagem($link)){

					$link_id = self::getCache($link);
					if($link_id==false){
						$link_id = self::linkBUYEncoder($link);
						self::criarCache($link,$link_id);
					}

					if($link_id!="Invalido" && !empty($link_id))
						$the_content = str_replace($link,"https://www.linkbuy.com.br/file/$link_id".'-'."$PARCEIRO_ID/$post_title_slug",$the_content);
					else
						$the_content = str_replace($link,"https://www.linkbuy.com.br/top/?e=null_or_invalid",$the_content);
					
					//////// TARGET/REL
					preg_match_all("/rel[ ]*[=]([ ]*)([\"]|['])*([_])*([A-Za-z0-9])+([\"]|['])*/i",$the_content,$saida_rel);
					$the_content = str_replace(array($saida_rel[0][0],'<a'),array("","<a rel='nofollow'"),$the_content);			
					preg_match_all("/target[ ]*[=]([ ]*)([\"]|['])*([_])*([A-Za-z0-9])+([\"]|['])*/i",$the_content,$saida_target);
					$the_content = str_replace(array($saida_target[0][0],'<a'),array("","<a target='_blank'"),$the_content);
					////////
			
				} // Imagens de exessao
			
			} // Dominios de exessao
			
		}
			if(self::getOpVideo())
				return "<div id='linkbuy_content'>".$the_content."</div>";
			else
				return $the_content;
	}
	// FINAL public function ProtetorLinkBUY
		
}// Final da Classe
		
$pathPlugin = substr(strrchr(dirname(__FILE__),DIRECTORY_SEPARATOR),1).DIRECTORY_SEPARATOR.basename(__FILE__);

// Função ativar
register_activation_hook( $pathPlugin, array('LinkBuy','ativar'));
// Função desativar
register_deactivation_hook( $pathPlugin, array('LinkBuy','desativar'));
//Ação de criar menu
add_action('admin_menu', array('LinkBuy','criarMenu'));

//Filtro do conteúdo
if(get_option('linkbuy_ParceiroID')!=NULL && get_option('linkbuy_ParceiroID')!=0 && get_option('linkbuy_ParceiroURL')!=NULL){
	add_filter("the_content", array('LinkBuy','AtivarPlugin'));
	add_action('wp_head', array('LinkBuy','LinkBuyHeaders'));
}
?>