#!/bin/bash

js_file="translations_tmp.js"

grep -v '^export' $1 > ${js_file}
echo "console.log(JSON.stringify({ texts }, null, 2));" >> ${js_file}

# Ensure yq is installed
if ! command -v yq &> /dev/null
then
    echo "yq could not be found. Please install yq and try again."
    exit 1
fi

json_output=$(node "$js_file")

if ! echo "$json_output" | jq . > /dev/null 2>&1; then
    echo "Invalid JSON output. Exiting."
    exit 1
fi


echo "$json_output" | yq e -P > temp.yaml

if [ ! -f temp.yaml ]; then
    echo "temp.yaml was not created. Exiting."
    exit 1
else
  languages=$(echo "$json_output" | jq -r '.texts | keys[]')
  for lang in $languages; do
      yq e ".texts.${lang}" < temp.yaml > "groupifier/${lang}.yml"
  done
fi

rm temp.yaml ${js_file}

echo "Conversion complete. YAML files have been created for each language."
