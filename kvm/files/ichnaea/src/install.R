options("repos"="http://cran.us.r-project.org")

install.package.version <- function(
  package,
  version = NULL,
  repos = getOption('repos'),
  type = getOption("pkgType"))
{
  contriburl <- contrib.url(repos, type)
  available <- available.packages(contriburl)
  if (package %in% row.names(available)) {
    current.version <- available[package, 'Version']
    if (is.null(version) || version == current.version) {
      install.packages(package, repos = repos, contriburl = contriburl, type = type)
      return()
    }
  }
 
  con <- gzcon(url(sprintf("%s/src/contrib/Meta/archive.rds", repos), "rb"))
  archive <- readRDS(con)
  close(con)
 
  if (!(package %in% names(archive))) {
    stop(sprintf("couldn't find package '%s'", package))
  }
  info <- archive[[package]]
 
  if (is.null(version)) {
    # Just grab the latest one. This will only happen if the package
    # has been pulled from CRAN.
    package.path <- info[length(info)]
  } else {
    package.path <- paste(package, "/", package, "_", version, ".tar.gz", sep="")
    if (!(package.path %in% names(info))) {
      stop(sprintf("version '%s' is invalid for package '%s'", version, package))
    }
  }
  package.url <- sprintf("%s/src/contrib/Archive/%s", repos, package.path)
  local.path <- file.path(tempdir(), basename(package.path))
  if (download.file(package.url, local.path) != 0) {
    stop("couldn't download file: ", package.url)
  }
 
  install.packages(local.path, repos = repos, type = type)
}

install.package.version("rvmbinary", "0.1.2")
install.packages("pnn")
install.packages("e1071")
install.packages("ipred")
install.packages("kernlab")
install.packages("mlbench")
install.packages("prodlim")
install.packages("TunePareto")
# XML needs libxml2-dev installed to compile
install.packages("XML")
install.packages("StatDataML")