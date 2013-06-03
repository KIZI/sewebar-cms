package izi_repository.clustering.service;

import izi_repository.clustering.computing.ClusterCharacteristicsComputer;
import izi_repository.clustering.computing.ResultCharacteristicsComputer;
import izi_repository.domain.Centroid;
import izi_repository.domain.Cluster;
import izi_repository.domain.arbquery.ArBuilderQuery;
import izi_repository.domain.arbquery.Params;
import izi_repository.domain.arbquery.QuerySettings;
import izi_repository.domain.result.Result;
import izi_repository.fuzzysearch.service.FuzzySearchService;
import izi_repository.logging.event.EventLogger;

import java.util.ArrayList;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;


/**
 * Implementation of {@link ClusteringService}.
 * 
 * @author Tomas Marek
 * 
 */
public class ClusteringServiceImpl implements ClusteringService {

	@Autowired
	private EventLogger logger;
	
	@Autowired
	private FuzzySearchService fuzzySearchService;
	
	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Cluster> getClustersByQuery(ArBuilderQuery query, QuerySettings settings) {
		List<Result> results = fuzzySearchService.getFuzzyResultsByQuery(query, settings);
		return clusterResults(results, settings.getParams(), new ArrayList<Cluster>());
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Cluster> clusterResults(List<Result> results, Params params, List<Cluster> clusters) {
		if (results == null) {
			logger.logInfo(this.getClass().toString(), "CLUSTERING - Results null!");
			return clusters;
		}
		
		double belongingLimit;
		if (params != null && params.getClusterBelongingLimit() != 0) {
			belongingLimit = params.getClusterBelongingLimit();
		} else {
			belongingLimit = 0.9;
		}
		
		String formulaType = null;
		if (params != null) {
			formulaType = params.getClusterDistanceFormula();
		}

		logger.logInfo(this.getClass().toString(), "CLUSTERING - Settings: BelongingLimit = " + belongingLimit + " | DistanceFormula = " + formulaType);
		
		for (Result result : results) {
			if (clusters.size() == 0) {
				clusters.add(createNewCluster(result));
			} else {
				double[] distances = new double[clusters.size()];
				
				for (int i = 0; i < clusters.size(); i++) {
					distances[i] = ResultCharacteristicsComputer.compare(clusters.get(i).getCentroid(), result, formulaType);
				}
				
				int positionWithMaxDistance = getPositionWithMaxValue(distances);
				double maxDistance = distances[positionWithMaxDistance];
				
				if (maxDistance < belongingLimit) {
					clusters.add(createNewCluster(result));
				} else {
					clusters.get(positionWithMaxDistance).getResults().add(result);
					ClusterCharacteristicsComputer.updateCluster(clusters.get(positionWithMaxDistance));
				}
				
			}
		}
		
		List<Result> resultsToReprocess = getResultsToReproces(clusters, belongingLimit, formulaType);
		logger.logInfo(this.getClass().toString(), "CLUSTERING - Results to reprocess: " + resultsToReprocess.size());
		
		if (resultsToReprocess.size() > 0) {
			clusterResults(resultsToReprocess, params, clusters);
		}
		
		return clusters;
	}
	
	private Cluster createNewCluster(Result result) {
		Centroid centroid = new Centroid();
		centroid.setVector(result.getQueryCompliance());
		
		Cluster cluster = new Cluster();
		cluster.setCentroid(centroid);
		cluster.getResults().add(result);
		
		return cluster;
	}

	private int getPositionWithMaxValue(double[] array) {
		double maxValue = 0;
		int position = 0;
		for (int i = 0; i < array.length; i++) {
			if (array[i] > maxValue) {
				maxValue = array[i];
				position = i;
			}
		}
		return position;
	}
	
	private List<Result> getResultsToReproces(List<Cluster> clusters, double belongingLimit, String formulaType) {
		List<Result> ret = new ArrayList<Result>();
		if (clusters == null) {
			return ret;
		}
		for(Cluster cluster : clusters) {
			List<Result> clusterResults = cluster.getResults();
			Centroid centroid = cluster.getCentroid();
			if (clusterResults != null && centroid != null) {
				for (int i = 0; i < clusterResults.size(); i++) {
					Result result = clusterResults.get(i);
					double distance = ResultCharacteristicsComputer.compare(centroid, result, formulaType);
					if (distance < belongingLimit) {
						clusterResults.remove(i);						
						ret.add(result);
					}
				}
			}
		}
		return ret;
	}
}
