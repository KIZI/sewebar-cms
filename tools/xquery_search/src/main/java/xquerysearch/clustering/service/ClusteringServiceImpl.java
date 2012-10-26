package xquerysearch.clustering.service;

import java.util.ArrayList;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.domain.Cluster;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.Params;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.result.Result;
import xquerysearch.fuzzysearch.service.FuzzySearchService;
import xquerysearch.service.QueryService;

/**
 * Implementation of {@link ClusteringService}.
 * 
 * @author Tomas Marek
 * 
 */
public class ClusteringServiceImpl implements ClusteringService {

	@Autowired
	private QueryService queryService;
	
	@Autowired
	private FuzzySearchService fuzzySearchService;
	
	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Cluster> getClustersByQuery(ArBuilderQuery query, QuerySettings settings) {
		List<Result> results = fuzzySearchService.getFuzzyResultsByQuery(query, settings);
		return clusterResults(results, settings.getParams());
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Cluster> clusterResults(List<Result> results, Params params) {
		List<Cluster> clusters = new ArrayList<Cluster>();
		return clusters;
	}
}
