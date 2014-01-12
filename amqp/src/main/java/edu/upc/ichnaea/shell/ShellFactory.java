package edu.upc.ichnaea.shell;

import java.net.MalformedURLException;
import java.util.Map;

public class ShellFactory {

    public ShellInterface create(String url) throws MalformedURLException {
        if (url == null || url.length() == 0) {
            return new LocalShell();
        } else {
            return new SecureShell(url);
        }
    }

    public ShellInterface create(Map<String, String> options)
            throws MalformedURLException {
        if (options.get("url") != null || options.get("host") != null) {
            return SecureShell.create(options);
        }
        return new LocalShell();
    }
}