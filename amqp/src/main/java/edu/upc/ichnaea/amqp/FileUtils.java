package edu.upc.ichnaea.amqp;

import java.io.File;
import java.util.UUID;

public class FileUtils {

    public static String tempPath(String dir) {
        return new File(dir, UUID.randomUUID().toString()).getAbsolutePath();
    }

}
