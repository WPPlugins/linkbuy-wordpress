<?php 
if(!is_admin())
	exit;
	
function getDomain($url){
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

if(count($_POST)>0){

	extract($_POST);

	if($configuracoes_iniciais==true){

		if(isset($ParceiroID))
			update_option('linkbuy_ParceiroID', (int) $ParceiroID);
	
		if(isset($Excecoes)){
			$array_excecoes = explode(",",$Excecoes);
			$tamanho_array = sizeof($array_excecoes);
			if($tamanho_array>0){
				for($i=0;$i<$tamanho_array;$i++){
					$get_domain = getDomain($array_excecoes[$i]);
					if($get_domain==false)
						unset($array_excecoes[$i]);
					else
						$array_excecoes[$i] = $get_domain;
				}
				update_option('linkbuy_Excecoes',implode(",",$array_excecoes)); 
			}else
				update_option('linkbuy_Excecoes','');
		}

	$msg = '<div class="updated" id="message" style="padding:4px 6px">Suas Alterações em \'<strong>Configurações Iniciais</strong>\' foram Salvas!</div>';

	}else if($configuracoes_avancadas==true){
		
		if(isset($video))
			update_option('linkbuy_ck_op_video', true);
		else
			update_option('linkbuy_ck_op_video', false);
			
	$msg = '<div class="updated" id="message" style="padding:4px 6px">Suas Alterações em \'<strong>Configurações Avançadas</strong>\' foram Salvas!</div>';

	}else
		$msg = '<div class="updated" id="message"><p>Erro: Requisição Mal Formada!</p></div>';
}
?>
<div class="wrap">

<div id="icon-plugins" class="icon32"><br /></div>
<h2 class="no-border">LinkBuy Wordpress</h2><br/>

<p><img src="https://www.linkbuy.com.br/img/logo.png" alt="LinkBuy Wordpress" title="LinkBuy Wordpress" /></p><br/>

<?php echo $msg; ?>

<?php
$erros_criticos = array();
if(get_option('linkbuy_ParceiroID')==NULL || get_option('linkbuy_ParceiroID')==0){
	echo "<div id='message' class='error' style='padding:4px 6px'>'<strong>ID do Parceiro</strong>' está configurado incorretamente! <i>( <a href='http://www.linkbuy.com.br/parceiro/MeusDados.php?ref=WP-LinkBuy' target='_blank'>Pegue seu ID</a>: Painel Afiliado > Meus Dados > Dados Operacionais )</i></div>\n";
	$erros_criticos['linkbuy_ParceiroID'] = true;
}

if(get_option('linkbuy_ParceiroURL')==NULL){
	echo "<div id='message' class='error' style='padding:4px 6px'>'<strong>Seu Site</strong>' está configurado incorretamente!</div>\n";
	$erros_criticos['linkbuy_ParceiroURL'] = true;
}
	
if(sizeof($erros_criticos)==0)
	echo "<div id='message' class='updated' style='padding:4px 6px'>LinkBuy Wordpress está configurado e funcionando corretamente!</div>";
else
	echo "<div id='message' class='error' style='padding:4px 6px'>LinkBuy Wordpress não está configurado corretamente!</div>";
?>

<style>
.wrap {font-size:1.1em}
i {font-size:0.8em !important}
h2 {margin:29px 0 7px !important;padding:22px 0 0 !important;border-top:2px dashed #ccc}
.no-border{margin:5px 0 !important;border:none !important}
.input-disabled{background:#eee !important}
</style>

<br/>
<?php if(sizeof($erros_criticos)>0){ ?>
<b>&raquo; Cadastro:</b><br/>
Se você ainda não é Afiliado LinkBuy, faça seu <a href="http://www.linkbuy.com.br/parceiro/Cadastro.php" target="_blank">Cadastro</a>.
<?php }else{ ?>
<b>&raquo; Indique e Ganhe:</b><br/>
http://www.linkbuy.com.br/?aff=<?php echo get_option('linkbuy_ParceiroID'); ?>
<?php } ?>

<h2>&raquo; Configurações Iniciais</h2>
Necessário para colocar o Plugin em funcionamento.<br/><br/>

<form action="" method="post">
<input type="text" style="display:none" name="configuracoes_iniciais" value="true" />

<p><b>Seu Site:</b> 
<input type="text" disabled="disabled" class="input-disabled" value="<?php echo get_option('linkbuy_ParceiroURL');?>"/></p>

<p><b>ID do Afiliado:</b> 
<input type="text" name="ParceiroID" value="<?php echo get_option('linkbuy_ParceiroID');?>"/> 
<i>( <a href='http://www.linkbuy.com.br/parceiro/MeusDados.php?ref=WP-LinkBuy' target='_blank'>Pegue seu ID</a>: Painel Afiliado > Meus Dados > Dados Operacionais )</i></p>

<p><b>Domínios para Não Codificar:</b> 
<input type="text" name="Excecoes" value="<?php echo get_option('linkbuy_Excecoes');?>"/> <i>( Domínios que não deverã ser codificados. Ex: zura.com.br,lomadee.com.br,buscape.com.br )</i><p/>

<p><input type="submit" name="Submit" class="button-primary" value="Salvar Configurações" /></p>
</form>

</form>

<h2>&raquo; Contato</h2>

<p>Está com dificuldades para configurar o plugin? Acesse nosso <a href="http://www.linkbuy.com.br/suporte" target="_blank">Suporte Técnico</a>.
<br/>
Encontrou problemas ou algum bug? Envie um email para: <b>suporte@linkbuy.com.br</b></p>

</div>