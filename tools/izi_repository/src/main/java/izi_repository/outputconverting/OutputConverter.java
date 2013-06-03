package izi_repository.outputconverting;

import izi_repository.domain.output.BBA;
import izi_repository.domain.output.DBA;
import izi_repository.domain.output.Hit;
import izi_repository.domain.result.Cedent;
import izi_repository.domain.result.Result;
import izi_repository.domain.result.Rule;
import izi_repository.transformation.ResultForOutputObjectConverter;

import java.util.ArrayList;
import java.util.List;


/**
 * Converter for result objects to output objects.
 * 
 * @author Tomas Marek
 * 
 */
public class OutputConverter {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private OutputConverter() {
	}

	public static void convertObjects(List<BBA> bbas, List<DBA> dbas, List<Hit> hits, List<? extends Object> objects) {
		IdCounter id = new IdCounter(1);
		if (objects != null) {
			for (Object object : objects) {
				if (object instanceof Result) {
					convertResult(bbas, dbas, hits, ((Result) object), id);
				}
			}
		}
	}

	public static void convertResults(List<BBA> bbas, List<DBA> dbas, List<Hit> hits, List<Result> results) {
		IdCounter id = new IdCounter(1);
		if (results != null) {
			for (Result result : results) {
				convertResult(bbas, dbas, hits, result, id);
			}
		}
	}

	/**
	 * Fills given lists with data from given list of {@link Result}.
	 * 
	 * @param bbas
	 * @param dbas
	 * @param hits
	 * @param results
	 */
	private static void convertResult(List<BBA> bbas, List<DBA> dbas, List<Hit> hits, Result result, IdCounter id) {

		Hit hit = ResultForOutputObjectConverter.convert(result);

		Rule rule = result.getRule();

		if (rule != null) {
			String antecedentId = null;
			String consequentId = null;
			String conditionId = null;

			Cedent antecedent = rule.getAntecedent();
			Cedent consequent = rule.getConsequent();
			Cedent condition = rule.getCondition();

			if (antecedent != null) {
				// id.increment();
				convertDbaListFirstLevel(antecedent.getDbas(), dbas, bbas, id);
				antecedentId = id.toString();
			}
			if (consequent != null) {
				// id.increment();
				convertDbaListFirstLevel(consequent.getDbas(), dbas, bbas, id);
				consequentId = id.toString();
			}
			if (condition != null) {
				// id.increment();
				convertDbaListFirstLevel(condition.getDbas(), dbas, bbas, id);
				conditionId = id.toString();
			}

			hit.setAssociationRule(ResultForOutputObjectConverter.convert(rule, antecedentId, consequentId,
					conditionId));
		}
		hits.add(hit);
	}

	private static void convertDbaListFirstLevel(List<izi_repository.domain.result.DBA> dbasResult, List<DBA> dbas, List<BBA> bbas, IdCounter id) {
		List<String> baRefs = convertDbaList(dbasResult, dbas, bbas, id);
		dbas.add(ResultForOutputObjectConverter.convert("Conjunction", id.toString(), baRefs));
	}

	private static List<String> convertDbaList(List<izi_repository.domain.result.DBA> dbasResult, List<DBA> dbas, List<BBA> bbas, IdCounter id) {
		List<String> localBaRefs = new ArrayList<String>();

		if (dbasResult == null || dbas == null) {
			return localBaRefs;
		}

		for (izi_repository.domain.result.DBA dba : dbasResult) {
			List<String> loopBaRefs = new ArrayList<String>();

			if (dba.getDbas() != null && dba.getDbas().size() > 0) {
				loopBaRefs = convertDbaList(dba.getDbas(), dbas, bbas, id);
			} else if (dba.getBbas() != null && dba.getBbas().size() > 0) {
				loopBaRefs = convertBbaList(bbas, dba.getBbas(), id);
			}

			DBA dbaOut = ResultForOutputObjectConverter.convert(dba.getConnective(), id.toString(),
					loopBaRefs);

			dbas.add(dbaOut);
			localBaRefs.add(id.toString());
			id.increment();
		}

		return localBaRefs;
	}

	private static List<String> convertBbaList(List<BBA> bbas, List<izi_repository.domain.result.BBA> bbasResult, IdCounter id) {
		List<String> localBaRefs = new ArrayList<String>();

		for (izi_repository.domain.result.BBA resultBba : bbasResult) {
			BBA bbaOut = ResultForOutputObjectConverter.convert(resultBba, id.toString());
			bbaOut.setId(id.toString());
			bbas.add(bbaOut);
			localBaRefs.add(id.toString());
			id.increment();
		}

		return localBaRefs;
	}
}
