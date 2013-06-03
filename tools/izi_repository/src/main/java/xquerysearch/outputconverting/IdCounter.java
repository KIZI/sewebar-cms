package xquerysearch.outputconverting;

/**
 * Object for keeping IDs when transforming output.
 * 
 * @author Tomas Marek
 * 
 */
public class IdCounter {

	private int id;

	/**
	 * @param id set id to start from
	 */
	public IdCounter(int id) {
		this.id = id;
	}
	
	/**
	 * @return the id
	 */
	public int getId() {
		return id;
	}

	/**
	 * @param id
	 *            the id to set
	 */
	public void setId(int id) {
		this.id = id;
	}

	/**
	 * Increments id by 1.
	 */
	public void increment() {
		id++;
	}
	
	/**
	 * {@inheritDoc}
	 */
	@Override
	public String toString() {
		return Integer.toString(id);
	}
}
