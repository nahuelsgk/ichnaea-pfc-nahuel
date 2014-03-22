package edu.upc.ichnaea.amqp;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

public class IOUtils {

    public static byte[] read(Object in) throws IOException {
        if (in instanceof InputStream) {
            return read((InputStream) in);
        }
        if (in instanceof String) {
            return ((String) in).getBytes();
        }
        throw new IOException("Could not read object");
    }

    public static void write(OutputStream out, byte[] data) throws IOException {
        for (int i = 0; i < data.length; i++)
        {
            out.write(data[i]);
        }
    }

    public static byte[] read(InputStream in) throws IOException {
        ByteArrayOutputStream out = new ByteArrayOutputStream();

        byte[] buffer = new byte[1024];
        int len = 0;
        while ((len = in.read(buffer)) != -1) {
            out.write(buffer, 0, len);
        }

        return out.toByteArray();
    }
}
