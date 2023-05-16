#!/bin/sh

cd /tmp
wget 'https://www.worldcubeassociation.org/export/results/WCA_export.sql.zip' -O /tmp/WCA_export.sql.zip
unzip WCA_export.sql.zip
cd /var/www/html/qhube.bzh
bin/console app:import-wca
