#!/bin/bash

pushd "${DDEV_DOCROOT}"

wp plugin install https://github.com/Rarst/wps/releases/latest/download/wps.zip --network-activate
