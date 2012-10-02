package xquerysearch.clustering.service;

import java.util.List;

import xquerysearch.domain.Cluster;
import xquerysearch.domain.result.ResultSet;

/**
 * Service providing clustering.
 * 
 * @author Tomas Marek
 *
 */
public interface ClusteringService {

	/**
	 * Creates {@link Cluster}s from {@link ResultSet}.
	 * 
	 * @param resultSet
	 * @return
	 */
	public List<Cluster> getClustersForResultSet(ResultSet resultSet);
	
}
