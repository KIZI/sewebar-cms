package izi_repository.utils;

import izi_repository.domain.result.BBA;
import izi_repository.domain.result.BBAForAnalysis;
import izi_repository.domain.result.Cedent;
import izi_repository.domain.result.DBA;
import izi_repository.domain.result.Result;

import java.util.ArrayList;
import java.util.Collection;
import java.util.List;


/**
 * Class providing helping methods for using {@link Result}s.
 * 
 * @author Tomas Marek
 * 
 */
public class ResultUtils {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private ResultUtils() {
	}

	/**
	 * Retrieves all {@link BBA}s from given {@link Result}.
	 * 
	 * @param result
	 * @return
	 */
	public static List<BBA> getBbasFromResult(Result result) {
		if (result == null) {
			return null;
		}

		List<BBA> ret = new ArrayList<BBA>();

		if (result.getRule() != null) {
			ret.addAll(getBbasFromCedent(result.getRule().getAntecedent()));
			ret.addAll(getBbasFromCedent(result.getRule().getConsequent()));
			ret.addAll(getBbasFromCedent(result.getRule().getCondition()));
		}

		return ret;
	}

	/**
	 * Goes through cedent's {@link DBA}s and retrieves their {@link BBA}s.
	 * 
	 * @param cedent
	 * @return
	 */
	public static List<BBA> getBbasFromCedent(Cedent cedent) {
		List<BBA> ret = new ArrayList<BBA>();
		if (cedent != null) {
			List<DBA> dbas = cedent.getDbas();
			if (dbas != null) {
				for (DBA dba : dbas) {
					ret.addAll(getBbasFromDba(dba));
				}
			}
		}
		return ret;
	}

	/**
	 * Goes through {@link DBA}s and retrieves their {@link BBA}s.
	 * 
	 * @param dba
	 * @return
	 */
	public static List<BBA> getBbasFromDba(DBA dba) {
		List<BBA> ret = new ArrayList<BBA>();
		if (dba != null) {
			List<BBA> bbas = dba.getBbas();
			List<DBA> dbas = dba.getDbas();

			if (bbas != null) {
				for (BBA bba : bbas) {
					ret.add(bba);
				}
			}

			if (dbas != null) {
				for (DBA dbaOfDba : dbas) {
					ret.addAll(getBbasFromDba(dbaOfDba));
				}
			}
		}
		return ret;
	}

	/**
	 * Retrieves all {@link BBAForAnalysis}s from given {@link Result}.
	 * 
	 * @param result
	 * @return
	 */
	public static List<BBAForAnalysis> getBbasForAnalysisFromResult(Result result) {
		if (result == null) {
			return null;
		}

		List<BBAForAnalysis> ret = new ArrayList<BBAForAnalysis>();

		if (result.getRule() != null) {
			ret.addAll(getBbasForAnalysisFromCedent(result.getRule().getAntecedent(), false));
			ret.addAll(getBbasForAnalysisFromCedent(result.getRule().getConsequent(), false));
			ret.addAll(getBbasForAnalysisFromCedent(result.getRule().getCondition(), false));
		}

		return ret;
	}

	/**
	 * Goes through cedent's {@link DBA}s and retrieves their {@link BBAForAnalysis}s.
	 * 
	 * @param cedent
	 * @return
	 */
	public static List<BBAForAnalysis> getBbasForAnalysisFromCedent(Cedent cedent, boolean disjunctive) {
		List<BBAForAnalysis> ret = new ArrayList<BBAForAnalysis>();
		if (cedent != null) {
			List<DBA> dbas = cedent.getDbas();
			if (dbas != null) {
				for (DBA dba : dbas) {
					boolean isDbaDisjunctive = false;
					if (dba.getConnective().equalsIgnoreCase("disjunction")) {
						isDbaDisjunctive = true;
					}
					ret.addAll(getBbasForAnalysisFromDba(dba, isDbaDisjunctive));
				}
			}
		}
		return ret;
	}

	/**
	 * Goes through {@link DBA}s and retrieves their {@link BBAForAnalysis}s.
	 * 
	 * @param dba
	 * @return
	 */
	public static List<BBAForAnalysis> getBbasForAnalysisFromDba(DBA dba, boolean disjunctive) {
		List<BBAForAnalysis> ret = new ArrayList<BBAForAnalysis>();
		if (dba != null) {
			List<BBA> bbas = dba.getBbas();
			List<DBA> dbas = dba.getDbas();

			if (bbas != null) {
				for (BBA bba : bbas) {
					BBAForAnalysis bbaForAnalysis = new BBAForAnalysis(bba);
					bbaForAnalysis.setDisjunctive(disjunctive);
					ret.add(bbaForAnalysis);
				}
			}

			if (dbas != null) {
				for (DBA dbaOfDba : dbas) {
					boolean isDbaDisjunctive = false;
					if (dbaOfDba.getConnective().equalsIgnoreCase("disjunction")) {
						isDbaDisjunctive = true;
					}
					ret.addAll(getBbasForAnalysisFromDba(dbaOfDba, isDbaDisjunctive));
				}
			}
		}
		return ret;
	}
	
	/**
	 * Retrieves all categories from given {@link BBA}s.
	 * 
	 * @param bbas
	 * @return
	 */
	public static List<String> getAllCategoriesFromBbas(Collection<BBA> bbas) {
		if (bbas == null) {
			return null;
		}

		List<String> ret = new ArrayList<String>();

		for (BBA bba : bbas) {
			if (bba.getTransformationDictionary() != null
					&& bba.getTransformationDictionary().getCatNames() != null) {
				ret.addAll(bba.getTransformationDictionary().getCatNames());
			}
		}

		return ret;
	}

	/**
	 * Retrieves all categories from given {@link BBA}s having specified
	 * FieldRef.
	 * 
	 * @param bbas
	 * @return
	 */
	public static List<String> getCategoriesFromBbasByFieldRef(Collection<BBA> bbas, String fieldRef) {
		if (bbas == null) {
			return null;
		}

		List<String> ret = new ArrayList<String>();

		for (BBA bba : bbas) {
			if (bba.getTransformationDictionary() != null
					&& bba.getTransformationDictionary().getCatNames() != null
					&& bba.getTransformationDictionary().getFieldName().equals(fieldRef)) {
				ret.addAll(bba.getTransformationDictionary().getCatNames());
			}
		}

		return ret;
	}

	/**
	 * Retrieves all categories from given {@link BBA}s.
	 * 
	 * @param bbas
	 * @return
	 */
	public static List<String> getAllFieldRefsFromBbas(Collection<BBA> bbas) {
		if (bbas == null) {
			return null;
		}
		List<String> ret = new ArrayList<String>();
		for (BBA bba : bbas) {
			String fieldName = bba.getTransformationDictionary().getFieldName();
			if (fieldName != null) {
				ret.add(fieldName);
			}
		}
		return ret;
	}

}
