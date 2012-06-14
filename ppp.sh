#!/usr/bin/env bash

# Php Pre Processor

file=$1;
dest=$2;

if [ -n "$file" ]; then

 cp ${file} ${file}.pp
 list=$(grep 'require' ${file} | sed 's/\s*require\s*(\s*"\(.*\)"\s*)\s*;\s*/\1/')

 for inc in ${list}; do
     escName=${inc/\//\\\/};
     escName=${escName/./\\.};
     sed -i -e "/\s*require\s*(\s*\"${escName}\"\s*)\s*;\s*/r ${inc}" \
	 -e "/\s*require\s*(\s*\"${escName}\"\s*)\s*;\s*/d" ${file}.pp
     sed -i '/<\?php \/\*DEL\*\//d' ${file}.pp
     sed -i '/\/\*\*\/?>/d' ${file}.pp
 done

 if [ -n "$dest" ]; then
     mv ${file}.pp ${dest}
 fi

fi