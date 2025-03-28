#!/bin/bash

languageFile="languages.txt"
rm $languageFile
for file in `find -iname "*.yml"`; do
  language=$(basename "$file" | cut -d. -f1)
  echo $language >> ${languageFile}_tmp
done
for file in `find -iname "*.tsv"`; do
  languages=$(head -n1 "$file")
  for language in ${languages[@]}; do
    if [ $language != 'key' ]; then
      echo $language >> ${languageFile}_tmp
    fi
  done
done

cat ${languageFile}_tmp | sort | uniq > $languageFile
rm ${languageFile}_tmp
