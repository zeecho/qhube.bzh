#!/bin/bash

project_folder="/var/www/html/qhube.bzh/data/translation_sources"
git_folder="~/git-projects"
wca="worldcubeassociation.org"
tnoodle="tnoodle"
afs="speedcubingfrance.org"
groupifier="groupifier"

cd ${git_folder}

# WCA
cd ${wca}
git pull
mkdir -p ${project_folder}/wca
cp config/locales/*.yml ${project_folder}/wca
rm ${project_folder}/wca/*.*.yml
# this is used to fix the missing spaces in multilines (the search breaks without this)
sed 's/^<p>/        <p>/g' -i ${project_folder}/wca/en.yml

# Tnoodle
cd ../${tnoodle}
git pull
mkdir -p ${project_folder}/tnoodle
cp server/src/main/resources/i18n/*.yml ${project_folder}/tnoodle

# AFS
cd ../${afs}
git pull
mkdir -p ${project_folder}/afs
cp config/locales/*.yml ${project_folder}/afs

# Groupifier
cd ../${groupifier}
git pull
mkdir -p ${project_folder}/groupifier
cd ${project_folder}
cp config/locales/*.yml ${project_folder}/groupifier
./generate_groupifier.sh /root/git-projects/groupifier/src/logic/translations.js
cd -
