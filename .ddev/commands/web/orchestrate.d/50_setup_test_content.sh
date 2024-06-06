#!/bin/bash

pushd "${DDEV_DOCROOT}"

wp user create usite1 usite1@example.com --role=administrator --porcelain --url="${DDEV_PRIMARY_URL}/site1"
wp user create usite2 usite2@example.com --role=administrator --porcelain --url="${DDEV_PRIMARY_URL}/site2"
wp user create usite3 usite3@example.com --role=administrator --porcelain --url="${DDEV_PRIMARY_URL}/site3"
wp user create usite4 usite4@example.com --role=administrator --porcelain --url="${DDEV_PRIMARY_URL}/site4"
wp user create usite5 usite5@example.com --role=administrator --porcelain --url="${DDEV_PRIMARY_URL}/site5"

wp site create --slug="site1" --email=usite1@example.com
wp site create --slug="site2" --email=usite2@example.com
wp site create --slug="site3" --email=usite3@example.com
wp site create --slug="site4" --email=usite4@example.com
wp site create --slug="site5" --email=usite5@example.com

wp user set-role ${ADMIN_USER} administrator --url="${DDEV_PRIMARY_URL}/site1"
wp user set-role ${ADMIN_USER} administrator --url="${DDEV_PRIMARY_URL}/site2"
wp user set-role ${ADMIN_USER} administrator --url="${DDEV_PRIMARY_URL}/site3"
wp user set-role ${ADMIN_USER} administrator --url="${DDEV_PRIMARY_URL}/site4"
wp user set-role ${ADMIN_USER} administrator --url="${DDEV_PRIMARY_URL}/site5"

wp plugin activate hello --url="${DDEV_PRIMARY_URL}/site2"
wp plugin activate hello --url="${DDEV_PRIMARY_URL}/site4"

wp theme install twentytwentyone --force
wp theme install twentytwentytwo --force
wp theme install twentytwentythree --force
wp theme install twentytwentyfour --force

wp theme enable twentytwentyfour --network
wp theme enable twentytwentythree --network
wp theme enable twentytwentytwo --network
wp theme enable twentytwentyone --network

wp theme activate twentytwentyfour --url="${DDEV_PRIMARY_URL}/site4"
wp theme activate twentytwentythree --url="${DDEV_PRIMARY_URL}/site3"
wp theme activate twentytwentytwo --url="${DDEV_PRIMARY_URL}/site2"
wp theme activate twentytwentyone --url="${DDEV_PRIMARY_URL}/site1"


