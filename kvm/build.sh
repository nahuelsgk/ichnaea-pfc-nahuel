#!/bin/bash

FILESFILE=`tempfile`
FILESPATH=files
while IFS= read -r -u3 -d $'\0' FILE; do
    FILE=`echo $FILE | sed -e "s@$FILESPATH/@@g"`
    FILE=`printf %q "$FILE"`
    echo "$FILESPATH/$FILE /usr/local/ichnaea/$FILE";
done 3< <(find $FILESPATH -type f -print0)

#ubuntu-vm-builder kvm precise \
#                  --domain ichnaea.lsi.upc.edu \
#                  --arch x86_64 \
#                  --hostname ichnaea \
#                  --mem 256 \
#                  --user ichnaea \
#                  --pass ichnaea \
#                  --mirror http://es.archive.ubuntu.com/ubuntu \
#                  --components main \
#                  --addpkg 'r-base' \ 
#                  --addpkg 'r-cran-car' \ 
#                  --removepkg cron \
#                  --copy $FILES
