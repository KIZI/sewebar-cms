package xquerysearch.utils;

import java.util.ArrayList;
import java.util.Collection;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

import xquerysearch.domain.result.BBA;
import xquerysearch.domain.result.Cedent;
import xquerysearch.domain.result.DBA;
import xquerysearch.domain.result.Result;

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
	 * TODO documentation
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
	public static Set<BBA> getBbasFromCedent(Cedent cedent) {
		Set<BBA> ret = new HashSet<BBA>();
		if (cedent != null) {
			Set<DBA> dbas = cedent.getDbas();
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
	public static Set<BBA> getBbasFromDba(DBA dba) {
		Set<BBA> ret = new HashSet<BBA>();
		if (dba != null) {
			Set<BBA> bbas = dba.getBbas();
			Set<DBA> dbas = dba.getDbas();

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
	 * TODO documentation
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
			if (bba.getTransformationDictionary() != null && bba.getTransformationDictionary().getCatNames() != null) {
				ret.addAll(bba.getTransformationDictionary().getCatNames());
			}
		}
		
		return ret;
	}
	
	/**
	 * TODO documentation
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
			if (bba.getTransformationDictionary() != null && bba.getTransformationDictionary().getCatNames() != null && bba.getTransformationDictionary().getFieldName().equals(fieldRef)) {
				ret.addAll(bba.getTransformationDictionary().getCatNames());
			}
		}
		
		return ret;
	}
	
}
