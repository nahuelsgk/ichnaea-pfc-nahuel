package edu.upc.ichnaea.amqp.model;

import java.util.Collection;
import java.util.Map;
import java.util.Set;
import java.util.TreeMap;

public class DatasetRow implements Comparable<DatasetRow> {

    Map<String, DatasetCell> mCells;

    DatasetRow() {
        this(new TreeMap<String, DatasetCell>());
    }

    DatasetRow(Map<String, DatasetCell> cells) {
        mCells = cells;
    }

    public void put(String name, DatasetCell cell) {
        mCells.put(name, cell);
    }

    public Set<String> columns() {
        return mCells.keySet();
    }

    public Collection<DatasetCell> cells() {
        return mCells.values();
    }

    public DatasetCell get(String name) {
        return mCells.get(name);
    }

    public int size() {
        return mCells.size();
    }

    @Override
    public int compareTo(DatasetRow o) {
        int diff = size() - o.size();
        if (diff != 0) {
            return diff;
        }
        for (String name : mCells.keySet()) {
            DatasetCell cell = o.get(name);
            if (cell == null) {
                diff = 1;
            } else {
                diff = get(name).compareTo(cell);
            }
            if (diff != 0) {
                break;
            }
        }
        return diff;
    }
}
