package izi_repository.clustering.service;

import izi_repository.domain.Cluster;
import izi_repository.domain.arbquery.ArBuilderQuery;
import izi_repository.domain.arbquery.Params;
import izi_repository.domain.arbquery.QuerySettings;
import izi_repository.domain.result.Result;

import java.util.List;


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
	 * @param clusters
	 * @return
	 */
	public List<Cluster> clusterResults(List<Result> results, Params params, List<Cluster> clusters);
	
}
