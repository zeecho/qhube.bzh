#!/bin/bash

git_folder=$1
project_folder=$2
wca="worldcubeassociation.org"
tnoodle="tnoodle"
afs="speedcubingfrance.org"
groupifier="groupifier"

# WCA
cd ${git_folder}/${wca}
git pull
mkdir -p ${project_folder}/wca
cp config/locales/*.yml ${project_folder}/wca
rm ${project_folder}/wca/*.*.yml
# this is used to fix the missing spaces in multilines (the search breaks without this)
sed 's/^<p>/        <p>/g' -i ${project_folder}/wca/en.yml

# Tnoodle
cd ${git_folder}/${tnoodle}
git pull
mkdir -p ${project_folder}/tnoodle
cp server/src/main/resources/i18n/*.yml ${project_folder}/tnoodle

# AFS
cd ${git_folder}/${afs}
git pull
mkdir -p ${project_folder}/afs
cp config/locales/*.yml ${project_folder}/afs
sed 's/true:/"true":/g' ${project_folder}/afs/*.yml -i
sed 's/false:/"false":/g' ${project_folder}/afs/*.yml -i

# Groupifier
cd ${git_folder}/${groupifier}
git pull
mkdir -p ${project_folder}/groupifier
cd ${project_folder}
./generate_groupifier.sh ${git_folder}/groupifier/src/logic/translations.js
