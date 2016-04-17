#!/bin/bash


function echo_help_exit {

	echo ""
	echo -e "Modo de Uso:\t$(basename "${setup_file}") site_dir [mysql-args]\n"
	echo -e "El parametro mysql-args es opcional, es para conectarse a la bd (host, user, password, etc)\n"
	echo ""
	exit;
}


function echo_error_exit {

	echo -e "\nError: ${1}"
	echo_help_exit
	exit;

}


function copy_setup_file {

	src_file="${setup_file_dir}/files/${1}"
	dest_file="${2}"
	overwrite="${3}"
	
	if [ "${overwrite}" == "" ]; then
		overwrite="${auto_overwrite}"
	fi;
	
	if [ "${overwrite}" == "true" ]; then
	
		cp "${src_file}" "${dest_file}";
		
	elif [ "${overwrite}" != "true" ]; then	
		
		if [ ! -e "${dest_file}" ]; then
	
			copy_setup_file "$1" "$2" "true";
		
		elif [ "$overwrite" != "false" ]; then
		
			#echo ""
			#read -e -p " El archivo \"${dest_file}\" ya existe, sobreescribir? [y/n/Y/N]: " overwrite
			#
			#overwrite="${overwrite:0:1}"
			#
			#if [ "${overwrite}" == "Y" ] || [ "${overwrite}" == "S" ] || [ "${overwrite}" == "T" ]; then
			#	auto_overwrite="true"
			#fi;
			#
			#if [ "${overwrite}" == "N" ]; then
			#	auto_overwrite="false"
			#fi;

			overwrite="n"
			overwrite="${overwrite,,}"
			
			if [ "${overwrite}" == "y" ] || [ "${overwrite}" == "s" ] || [ "${overwrite}" == "t" ]; then
				overwrite="true"
			else
				overwrite="false"
			fi;
			
			copy_setup_file "$1" "$2" "${overwrite}";
			
		fi;
		
	fi;
}


function create_folder {
	mkdir -p "${1}"
}

function create_control_folder {
	create_folder "${1}"
	create_folder "${1}/userinterface"
	create_folder "${1}/templates"
}

function read_confirm_prompt {
	
	message="${1} [y/n]: "
	read -e -p "${message}" read_response

	read_response="${read_response# }"
	read_response="${read_response% }"

	if [ -z "${read_response}" ]; then

		#read_confirm_prompt "$1"
		#echo "1"
		echo "0"

	else

		read_response="${read_response,,}"
		read_response="${read_response:0:1}"
		
		if [ "${read_response}" == 'y' ] || [ "${read_response}" == 's' ]; then

			echo "1"

		else

			echo "0"
			
		fi;

	fi;
}

function read_prompt {

	default_value="$2"

	default_value="${default_value# }"
	default_value="${default_value% }"

	if [ -z "${default_value}" ]; then
	
		message="${1}: "

	else

		message="${1} (${default_value}): "

	fi;

	read -e -p "${message}" read_response

	read_response="${read_response# }"
	read_response="${read_response% }"
	
	if [ -z "${read_response}" ]; then

		if [ -z "${2}" ]; then

			read_prompt "$1"

		else

			echo "${default_value}";

		fi;

	else

		echo "${read_response}"

	fi;
}

function parse_language {

	key="$1"
	key="${key,,}"
	key="${key// /}"

	if [ "${key}" == "${key//\-/}" ]; then

		echo "${key}";

	else

		language_code="${key%-*}";
		language_region="${key#*-}";

		echo "${language_code,,}-${language_region^^}"

	fi;
}

function config_site {

	debug_mode="$(read_confirm_prompt "General | Debug Mode")"
	live_mode="$(read_confirm_prompt "General | Live Mode")"
	development_mode="$(read_confirm_prompt "General | Development Mode")"
	server_name="$(read_prompt "General | Server Name")"
	site_name="$(read_prompt "General | Site Name" "${server_name}")"
	
	default_language="$(read_prompt "General | Default Language (format xx-XX o xx)" "es")"
	default_language="$(parse_language "${default_language}")"

	mysql_server="$(read_prompt "MySQL | Server" "localhost")"
	mysql_dbname="$(read_prompt "MySQL | Database")"
	mysql_user="$(read_prompt "MySQL | User")"
	mysql_pass="$(read_prompt "MySQL | Password")"
	mysql_port="$(read_prompt "MySQL | Port" "3306")"

	html_title="$(read_prompt "HTML | Page Title" "${site_name}")"
	html_keywords="$(read_prompt "HTML | Keywords" " ")"
	html_author="$(read_prompt "HTML | Author" "Zentefi")"
	html_description="$(read_prompt "HTML | Description" " ")"
	html_compress_html="$(read_confirm_prompt "HTML | Compress HTML")"
	html_compress_js="$(read_confirm_prompt "HTML | Compress JS")"
	html_compress_css="$(read_confirm_prompt "HTML | Compress CSS")"

	email_send="$(read_confirm_prompt "E-Mail | Enable Emails")"

	if [ "${email_send}" == "1" ]; then

		email_from_email="$(read_prompt "E-Mail | From E-Mail")"
		email_from_name="$(read_prompt "E-Mail | From Name" " ")"
		email_force_to="$(read_prompt "E-Mail | Force To E-Mail (All mails will be sent to this)" " ")"
		email_force_cco="$(read_prompt "E-Mail | Force CCO E-Mail (All mails will send a copy to this)" " ")"
		email_smtp_server="$(read_prompt "E-Mail | SMTP Server")"
		email_smtp_ssl="$(read_confirm_prompt "E-Mail | SMTP Server SSL Enabled")"

		if [ "${email_smtp_ssl}" == "1" ]; then

			email_smtp_port="$(read_prompt "E-Mail | SMTP Port" "465")"
			email_smtp_protocol="$(read_prompt "E-Mail | SMTP Protocol" "tls")"

		else

			email_smtp_port="$(read_prompt "E-Mail | SMTP Port" "25")"
			email_smtp_protocol="$(read_prompt "E-Mail | SMTP Protocol" " ")"

		fi;

		email_smtp_user="$(read_prompt "E-Mail | SMTP Login User" " ")"
		email_smtp_pass="$(read_prompt "E-Mail | SMTP Login Pass" " ")"

	else

		email_from_email=""
		email_from_name=""
		email_force_to=""
		email_force_cco=""
		email_smtp_server=""
		email_smtp_ssl=""
		email_smtp_port=""
		email_smtp_protocol=""
		email_smtp_user=""
		email_smtp_pass=""
	fi;

	cache_enabled="$(read_confirm_prompt "Cache | Enable Cache")"
	cache_memcached_host=""
	cache_memcached_port=""
	cache_memcached_timeout=""
	cache_system="file"

	if [ "${cache_enabled}" == "1" ]; then

		cache_memcached_enabled="$(read_confirm_prompt "Cache | Enable Memcached")"

		if [ "${cache_memcached_enabled}" == "1" ]; then
			cache_system="memcached"
			cache_memcached_host="$(read_prompt "Cache | Memcached Host" "127.0.0.1")"
			cache_memcached_port="$(read_prompt "Cache | Memcached Port" "11211")"
			cache_memcached_timeout="$(read_prompt "Cache | Memcached Timeout" "60")"
		fi;
	fi;

	access_control_enabled="$(read_confirm_prompt "Access Control | Enable Access Control")"
	access_control_user=""
	access_control_pass=""
	access_control_public_user=""
	access_control_public_pass=""
	access_control_admin_user=""
	access_control_admin_pass=""
	access_control_development_user=""
	access_control_development_pass=""

	if [ "${access_control_enabled}" == "1" ]; then

		access_control_user="$(read_prompt "Access Control | Access Control User" " ")"

		if [ ! -z "${access_control_user}" ]; then
			access_control_pass="$(read_prompt "Access Control | Access Control Pass" " ")"
		fi;

		access_control_public_user="$(read_prompt "Access Control | Access Control Public User" " ")"

		if [ ! -z "${access_control_public_user}" ]; then
			access_control_public_pass="$(read_prompt "Access Control | Access Control Public Pass" " ")"
		fi;

		access_control_admin_user="$(read_prompt "Access Control | Access Control Admin User" " ")"

		if [ ! -z "${access_control_admin_user}" ]; then
			access_control_admin_pass="$(read_prompt "Access Control | Access Control Admin Pass" " ")"
		fi;

		access_control_development_user="$(read_prompt "Access Control | Access Control Development User" " ")"

		if [ ! -z "${access_control_development_user}" ]; then
			access_control_development_pass="$(read_prompt "Access Control | Access Control Development Pass" " ")"
		fi;


	fi;

	multilanguage_enabled="$(read_confirm_prompt "MultiLanguage | Enable MultiLanguage Support")"

	if [ "${multilanguage_enabled}" == "1" ]; then

		languages="$(read_prompt "MultiLanguage | Languages, delimited by \",\" (format xx-XX o xx)" "${default_language}")"
		languages="${languages// /}"
		languages="${languages//,/ }"
		languages=( ${languages} )

		index_language=0
		for language in "${languages[@]}"; do
			languages["$index_language"]="$(parse_language "${language}")"
			index_language=$((index_language+1))
		done;

		languages_value="${languages[@]}"
		languages_value="${languages_value// /,}"
		languages="${languages_value}"

	else

		languages=""

	fi;	

	error_reporting_enabled="$(read_confirm_prompt "Errors | Enable Reporting")"
	error_reporting_email=""

	if [ "${error_reporting_enabled}" == "1" ]; then
		error_reporting_email="$(read_prompt "Errors | Email Recipient")"
	fi;


	config_file_contents="<?xml version=\"1.0\" encoding=\"iso-8859-15\"?>

<zphp_config>
	<debug_mode>${debug_mode}</debug_mode>
	<live_mode>${live_mode}</live_mode>
	<development_mode>${development_mode}</development_mode>
	<site_document_path></site_document_path>
	<server_name>${server_name}</server_name>
	<db>
		<debug>${debug_mode}</debug>
		<log>
			<errors>1</errors>
			<querys>0</querys>
		</log>
		<connection>
			<mysql>
				<server>${mysql_server}</server>
				<dbname>${mysql_dbname}</dbname>
				<user>${mysql_user}</user>
				<pass>${mysql_pass}</pass>
				<port>${mysql_port}</port>	
			</mysql>
		</connection>
	</db>
	<multi_language>
		<enabled>${multilanguage_enabled}</enabled>
		<languages>${languages}</languages>
		<default_language>${default_language}</default_language>
	</multi_language>	
	<html>
		<title>${html_title}</title>
		<keywords>ARRAY|${html_keywords}</keywords>
		<author>${html_author}</author>
		<description>${html_description}</description>
		<language>${default_language}</language>
		<compress>${html_compress_html}</compress>
		<use_min_js>${html_compress_js}</use_min_js>
		<use_min_css>${html_compress_css}</use_min_css>
	</html>
	<email>
		<log>
			<errors>1</errors>
			<commands>0</commands>
		</log>
		<send>${email_send}</send>
		<from>
			<name>${email_from_email}</name>
			<email>${email_from_name}</email>
		</from>
		<force_to>
			<email array=\"true\">${email_force_to}</email>
		</force_to>
		<force_cco>
			<email array=\"true\">${email_force_cco}</email>
		</force_cco>
		<smtp>
			<server>${email_smtp_server}</server>
			<ssl>${email_smtp_ssl}</ssl>
			<port>${email_smtp_port}</port>
			<protocol>${email_smtp_protocol}</protocol>
			<user>
				<login>${email_smtp_user}</login>
				<pass>${email_smtp_pass}</pass>
			</user>
		</smtp>
	</email>
	<cache>
		<enabled>${cache_enabled}</enabled>
		<system>${cache_system}</system>
		<system_memcached>
			<host>${cache_memcached_host}</host>
			<port>${cache_memcached_port}</port>
			<timeout>${cache_memcached_timeout}</timeout>
		</system_memcached>
	</cache>
	<error_reporting>
		<enabled>${error_reporting_enabled}</enabled>
		<recipients array=\"true\">ARRAY|${error_reporting_email}</recipients>
	</error_reporting>
	<access_control>
		<enabled>${access_control_enabled}</enabled>
		<user>${access_control_user}</user>
		<password>${access_control_pass}</password>";

	if [ ! -z "${access_control_public_user}" ]; then

		config_file_contents="${config_file_contents}
		<public>
			<user>${access_control_public_user}</user>
			<password>${access_control_public_pass}</password>
		</public>";

	fi;

	if [ ! -z "${access_control_admin_user}" ]; then

		config_file_contents="${config_file_contents}
		<admin>
			<user>${access_control_admin_user}</user>
			<password>${access_control_admin_pass}</password>
		</admin>";

	fi;

	if [ ! -z "${access_control_development_user}" ]; then

		config_file_contents="${config_file_contents}
		<development>
			<user>${access_control_development_user}</user>
			<password>${access_control_development_pass}</password>
		</development>";

	fi;


	config_file_contents="${config_file_contents}
	</access_control>	
</zphp_config>";

	echo "${config_file_contents}" > "${site_dir}/config.xml"


	if [ ! -e "${apache_file}" ]; then

		apache_address="$(read_prompt "Apache | Listen Address" "*")"
		apache_port="$(read_prompt "Apache | Listen Port" "80")"
		apache_server_alias="$(read_prompt "Apache | Server Alias" "${server_name}")"

		apache_conf_file_contents="<VirtualHost ${apache_address}:${apache_port}>

	ServerName ${server_name}
	ServerAlias ${apache_server_alias}
	DocumentRoot ${site_dir}/www

	SetEnv ZFRAMEWORK_APP_DIR ${site_dir}
	
	php_value auto_prepend_file ${zframework_dir}/init.php
	
</VirtualHost>";

		echo "${apache_conf_file_contents}" > "${apache_file}"


	fi;

	if [ ! -z "${mysql_server}" ] && [ ! -z "${mysql_user}" ]; then

		echo "Corriendo scripts SQL..."

		mysql_args="--default-character-set latin1 -h ${mysql_server} -u ${mysql_user} --password=\"${mysql_pass}\" -P ${mysql_port}"
		echo "DROP DATABASE IF EXISTS \`${mysql_dbname}\`" | eval "mysql ${mysql_args}"
		echo "CREATE DATABASE IF NOT EXISTS \`${mysql_dbname}\`" | eval "mysql ${mysql_args}"

		mysql_args="${mysql_args} ${mysql_dbname}"

		for sql_file in "${setup_file_dir}/files/sql/"*.sql; do
			cat "${sql_file}" | eval "mysql ${mysql_args}" &> /dev/null
		done;

		if [ ! -z "${default_language}" ]; then

			if [ "${default_language}" != "${default_language//\-/}" ]; then

				echo "UPDATE language SET is_default=1 WHERE id_language = '${default_language}'" | eval "mysql ${mysql_args}" &> /dev/null				

			else

				echo "UPDATE language SET is_default=1 WHERE id_language_code = '${default_language}'" | eval "mysql ${mysql_args}" &> /dev/null

			fi;

		fi;

		echo "${mysql_args}" > "${mysql_file}"

	else

		rm -rf "${mysql_file}"

	fi;


}

#-------------------------------------------------------------------------------------

setup_file=$(abs_path "$0");
setup_file_dir=$(dirname "${setup_file}");
zframework_dir=$(dirname "${setup_file_dir}");

if [ "$1" == "-h" ] || [ "$1" == "--help" ] || [ "$1" == "-help" ]; then
	echo_help_exit
fi;

if [ -z "${1}" ]; then

	echo_error_exit "Debe especificar el directorio del sitio"

fi;

site_dir="$(abs_path "$1")";
mkdir -p "${site_dir}"

backend_dir="${site_dir}/backend"
www_dir="${site_dir}/www"
mysql_file="${site_dir}/.mysql.args"
apache_file="${site_dir}/apache.conf"

cdw="${PWD}"
cd "${site_dir}"

mkdir -p "${backend_dir}"
mkdir -p "${backend_dir}/migrations"
mkdir -p "${www_dir}"
mkdir -p "${www_dir}/static"

auto_overwrite=""

create_folder "backups"

rm -rf "${www_dir}/static/zframework"
ln -s "${zframework_dir}/static" "${www_dir}/static/zframework"

if [ ! -e "${site_dir}/config.xml" ]; then

	config_site

else

	overwrite_config="$(read_confirm_prompt "Ya existe una configuracion para este sitio, sobreescribir?")"

	if [ "${overwrite_config}" == "1" ]; then
		config_site
	fi;

fi;

tempfile_name="$(tempfile )"
rm -rf "$tempfile_name"
mkdir -p "$tempfile_name"

tar -xf "${setup_file_dir}/files/files.tar" -C "$tempfile_name"
cp -ur "$tempfile_name"/* "${site_dir}"
rm -rf "$tempfile_name"

echo -e "\nSite created successful\n"

cd "${cdw}"
