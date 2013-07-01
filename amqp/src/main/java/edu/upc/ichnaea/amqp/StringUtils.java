package edu.upc.ichnaea.amqp;

import java.util.Collection;

public class StringUtils {

	static String join(Collection<String> parts, String sep) {
		String join = "";
		boolean start = true;
		for(String part : parts) {
			if(start) {
				start = false;
			} else {
				join += sep;
			}
			join += part;
		}
		return join;
	}
}
