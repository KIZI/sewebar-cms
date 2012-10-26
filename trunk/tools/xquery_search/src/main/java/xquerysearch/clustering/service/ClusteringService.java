package xquerysearch.clustering.service;

import java.util.List;

import xquerysearch.domain.Cluster;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.Params;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.result.Result;

/**
 * Service providing clustering.
 * 
 * @author Tomas Marek
 *
 */
public interface ClusteringService {

	/**
	 * Creates {@link Cluster}s from {@link Result}s retrieved by {@link ArBuilderQuery}.
	 * 
	 * @param query
	 * @param settings
	 * @return
	 */
	public List<Cluster> getClustersByQuery(ArBuilderQuery query, QuerySettings settings);
	
	/**
	 * Takes {@link List} of {@link Result}s and creates {@link Cluster}s from it.
	 * 
	 * @param results
	 * @param params
	 * @return
	 */
	public List<Cluster> clusterResults(List<Result> results, Params params);
	
}
