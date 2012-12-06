package edu.upc.ichnaea.shell;

import java.net.MalformedURLException;
import java.util.Map;

public class ShellFactory {
	public enum Type
	{
		Local,
		Remote
	}
	
	public ShellInterface create(Type type, Map<String, String> options) throws MalformedURLException
	{
		switch(type)
		{
		case Local:
			return new Shell();
		case Remote:
			return SecureShell.create(options); 
		}
		return null;
	}
}
