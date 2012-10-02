package xquerysearch.clustering.service;

import java.util.ArrayList;
import java.util.List;

import xquerysearch.domain.Cluster;
import xquerysearch.domain.result.ResultSet;

/**
 * Implementation of {@link ClusteringService}.
 * 
 * @author Tomas Marek
 * 
 */
public class ClusteringServiceImpl implements ClusteringService {

	/**
	 * @{inheritDoc
	 */
	@Override
	public List<Cluster> getClustersForResultSet(ResultSet resultSet) {
		List<Cluster> ret = new ArrayList<Cluster>();
		return ret;
	}

}
