#!/bin/bash


function echo_help_exit {

	echo ""
	echo -e "Modo de Uso:\t$(basename "${setup_file}") site_dir\n"
	echo ""
	exit;
}

function make_backup_zip
{
	site_dir="$1"
	zip_file="$2"
	remove_prefix="${site_dir}/"
	files=(  )

	for f in "${site_dir}"/*; do

		b="$(basename "$f")"
		if [ "$b" != "backups" ]; then
			files["${#files[@]}"]="${f#${remove_prefix}}"
		fi;

	done;

	zip -r "$zip_file" "${files[@]}" &> /dev/null

}

setup_file=$(abs_path "$0");
setup_file_dir=$(dirname "${setup_file}");

if [ "$1" == "-h" ] || [ "$1" == "--help" ] || [ "$1" == "-help" ]; then
	echo_help_exit
fi;

if [ -z "${1}" ]; then

	echo_error_exit "Debe especificar el directorio del sitio"

fi;

site_dir="$(abs_path "$1")";

if [ ! -d "$site_dir" ]; then

	echo -e "\nNo existe el sitio\n"

else

	backups_dir="${site_dir}/backups"
	mkdir -p "${backups_dir}"

	cdw="${PWD}"
	cd "${site_dir}"

	backup_prefix="backup-$(date '+%Y%m%d-%H%M')"

	
	mysql_args_file="${site_dir}/.mysql.args"

	if [ -e "${mysql_args_file}" ]; then

		mysql_args="$(cat "${mysql_args_file}")"
		eval "mysqldump ${mysql_args}" > "${backups_dir}/${backup_prefix}.sql"

	fi;

	make_backup_zip "${site_dir}" "${backups_dir}/${backup_prefix}.zip"

	echo -e "\nSe realizo el backup del sitio\n"

	cd "${cdw}"
fi;