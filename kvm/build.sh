#!/bin/bash

FILESFILE=`tempfile`
FILESPATH=files
while IFS= read -r -u3 -d $'\0' FILE; do
    FILE=`echo $FILE | sed -e "s@$FILESPATH/@@g"`
    FILE=`printf %q "$FILE"`
    echo "$FILESPATH/$FILE /usr/local/ichnaea/$FILE" >> $FILESFILE;
done 3< <(find $FILESPATH -type f -print0)

ADD_PACKAGES="r-base r-cran-car"
REMOVE_PACKAGES="cron"
TMPDIR=$1

ubuntu-vm-builder kvm precise \
                --verbose \
                --tmp $TMPDIR \
                --flavour virtual \
                --domain ichnaea.lsi.upc.edu \
                --arch amd64 \
                --hostname ichnaea \
                --mem 256 \
                --user ichnaea \
                --pass ichnaea \
                --mirror http://es.archive.ubuntu.com/ubuntu \
                --components main,universe \
                --copy $FILESFILE \
                --removepkg $REMOVE_PACKAGES \
                --addpkg $ADD_PACKAGES 
